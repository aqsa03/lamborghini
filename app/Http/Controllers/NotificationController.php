<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Models\Notification;
use App\Enums\NotificationType;
use App\Enums\NotificationStatus;
use App\Enums\NotificationTopic;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = Notification::latest();
        return view('notifications.index',[
                'total' => $notifications->count(),
                'notifications' => $notifications->paginate(20)
            ])
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("notifications.form", [
            'formType' => 'create',
            'types' => array_map(function($item) {
                return $item->value;
            }, NotificationType::cases()),
            'topics' => array_map(function($item) {
                return $item->value;
            }, NotificationTopic::cases()),
            'notification' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNotificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNotificationRequest $request)
    {
        $validatedFields = $request->validated();
        Notification::create($validatedFields);
        return redirect()->route('notifications.index')->with('success','Notification created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        return view("notifications.show", [
            'notification' => $notification
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        if($notification->status == NotificationStatus::SENT->value){
            return redirect()->route('notifications.index')->with('error','Unable to edit a sent notification.');
        }
        return view("notifications.form", [
            'formType' => 'edit',
            'notification' => $notification,
            'types' => array_map(function($item) {
                return $item->value;
            }, NotificationType::cases()),
            'topics' => array_map(function($item) {
                return $item->value;
            }, NotificationTopic::cases())
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreNotificationRequest  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(StoreNotificationRequest $request, Notification $notification)
    {
        //TODO filter message from tags and entities
        $validatedFields = $request->validated();
        $notification->update($validatedFields);
        return redirect()->route('notifications.index')->with('success','Notification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('notifications.index')
                        ->with('success','Notification deleted successfully');
    }
}
