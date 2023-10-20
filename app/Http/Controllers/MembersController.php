<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Kreait\Firebase\Auth\UserQuery;
use Illuminate\Validation\Rules\Password;

class MembersController extends Controller
{
    public function __construct()
    {
        $this->firebaseAuth = app('firebase.auth');
    }

    public function check_duplicated()
    {
        $active_customers = [];
        $duplicated_customers = [];
        $stripe = new \Stripe\StripeClient(
            config('stripe.apiToken')
        );
        $search_params = [
            'query' => 'status:\'active\' OR status:\'incomplete\' OR status:\'trialing\'',
            'limit' => 100
        ];
        
        $stripe_subscriptions = $stripe->subscriptions->search($search_params);

        $i = 1;
        do{
            if($i > 1){
                $search_params['page'] = $stripe_subscriptions->next_page;
            }
            $stripe_subscriptions = $stripe->subscriptions->search($search_params);
            foreach($stripe_subscriptions->data as $s){
                if(in_array($s->customer, $active_customers) and !in_array($s->customer, $duplicated_customers)){
                    array_push($duplicated_customers, $s->customer);    
                }
                array_push($active_customers, $s->customer);
            }
            $i++;
        }while($stripe_subscriptions->has_more);

        $duplicated_customers = array_map(function ($id) use ($stripe) {
            return $stripe->customers->retrieve($id);
        } , $duplicated_customers);

        return view('members.check_duplicated_subscription', [
            'customers' => $duplicated_customers,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = 50;
        $query = $query = UserQuery::all();
            // ->sortedBy(UserQuery::FIELD_CREATED_AT)
            // ->inDescendingOrder()
            // // ->inAscendingOrder() # this is the default
            // ->withOffset((request()->input('page', 1) - 1) * $limit)
            // ->withLimit($limit);
        if ($request->query("email")) {
            $query = ['filter' => [UserQuery::FILTER_EMAIL => $request->query('email')]];
        }
        $members = $this->firebaseAuth->queryUsers($query);
        $paginator = new Paginator([], $limit, request()->input('page', 1), ['path' => $request->url()]);
        if(count($members) == $limit){
            $paginator->hasMorePagesWhen(true);
        }
        return view('members.index', [
            'request' => $request,
            'members' => $members,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $member_id
     * @return \Illuminate\Http\Response
     */
    public function show(string $member_id)
    {
        $member = $this->firebaseAuth->getUser($member_id);
        $stripe_customer = false;
        $stripe_customer_payments = false;
        try{
            $stripe = $stripe = new \Stripe\StripeClient(
                config('stripe.apiToken')
            );
            $stripe_customer = $stripe->customers->search([
                'query' => 'metadata[\'firebaseUID\']:\''.$member_id.'\'',
            ])->data[0];
            $stripe_customer_payments = $stripe->paymentIntents->search([
                'query' => 'status:\'succeeded\' AND customer:\''.$stripe_customer->id.'\'',
                'limit' => 100,
            ]);
        } catch(\Exception $e){
            $stripe_customer = false;
            $stripe_customer_payments = false;
        }
        return view('members.show', [
            'member' => $member,
            'stripe_customer' => $stripe_customer,
            'stripe_customer_payments' => $stripe_customer_payments,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $member_id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $member_id)
    {
        return view('members.form', [
            'member' => $this->firebaseAuth->getUser($member_id),
            'formType' => 'edit'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $member_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $member_id)
    {

        if($request->input('change_password')){
            $validated = $request->validate([
                'new_password' => [
                    'required',
                    'string',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
                ],
            ]);
        }
        $emailVerified = empty($request->input('emailVerified')) ? false : true;
        $disabled = empty($request->input('disabled')) ? false : true;
        try{
            $this->firebaseAuth->updateUser($member_id, [
                'emailVerified' => $emailVerified,
                'disabled' => $disabled,
                'displayName' => $request->displayName
            ]);
            $customClaims = $request->input('roles_keys', false) ? array_filter(
                array_combine($request->input('roles_keys'), $request->input('roles_values')),
                function($k) {
                    return $k != '';
                },
                ARRAY_FILTER_USE_KEY
            ) : [];
            $this->firebaseAuth->setCustomUserClaims($member_id, $customClaims);
            if($request->input('new_password')){
                $this->firebaseAuth->changeUserPassword($member_id, $request->input('new_password'));
            }
            return redirect()->route('members.show', $member_id)->with('success', 'Membro aggiornato con successo');
        } catch (\Exception $e){
            return redirect()->route('members.show', $member_id)->with('error','Unable to update firebase auth user: '.$e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $member_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $member_id)
    {
        try {
            $this->firebaseAuth->deleteUser($member_id);
            return redirect()->route('members.index')->with('success', 'Membro cancellato con successo');
        } catch (\Exception $e){
            return redirect()->route('members.show', $member_id)->with('error','Unable to delete firebase auth user: '.$e->getMessage());
        }
    }
}
