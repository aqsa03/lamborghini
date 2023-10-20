<x-layouts.panel_layout>

<div class="body_section">

    <x-form_errors :errors="$errors"></x-form_errors>
    <form class="default_form" action="{{ $formType == 'create' ? route('members.store') : route('members.update', $member->uid) }}" method="POST">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="Display Name">
                Display Name
            </label>
            <input name="displayName" value="{{ $formType == 'create' ? '' : (old('displayName', $member?->displayName) ?? '') }}" class="form_input" type="text" placeholder="Display Name" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="emailVerified">
                Email verificata
            </label>
            <input {{ $formType == 'create' ? '' : (old('emailVerified', $member?->emailVerified) == 1 ? 'checked' : '') }} type="checkbox" name="emailVerified" value="1" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="disabled">
                Disabled
            </label>
            <input {{ $formType == 'create' ? '' : (old('disabled', $member?->disabled) == 1 ? 'checked' : '') }} type="checkbox" name="disabled" value="1" />
        </div>

        @if($formType == 'edit')
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="disabled">
                Change password
            </label>
            <input onclick="changePasswordOnClick()" type="checkbox" name="change_password" id="change_password" value="1" />
        </div>

        <div id="new_password_container" style="display:none" class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="Display Name">
                New Password
            </label>
            <input disabled name="new_password" id="new_password" class="form_input" type="password" placeholder="New Password" />
        </div>
        <script>
            const changePasswordOnClick = () => {
                const change_password = document.getElementById("change_password")
                if(change_password.checked){
                    document.getElementById("new_password_container").style.display = 'block';
                    document.getElementById("new_password").disabled = false;
                } else {
                    document.getElementById("new_password_container").style.display = 'none'
                    document.getElementById("new_password").disabled = true;
                }
            }
        </script>
        @endif

        <div class="w-full px-3 mt-12" x-data="claimsData({{ json_encode($member?->customClaims) }})">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="disabled">
                Ruoli
            </label>
            <ul id="customClaims">
                <template x-for="claim in claims" :key="claim['key']">
                    <div class="py-2">
                        <input class="border p-4" type="text" name="roles_keys[]" :value="claim['key']"> -&gt;
                        <input class="border p-4" type="text" name="roles_values[]" :value="claim['value']" />
                        <button class="delete_btn" @click.prevent="removeClaim" :data-claim-key="claim['key']">X</button>
                    </div>
                </template>
            </ul>
            <button class="btn_add mt-8" id="customClaimsAddBtn" @click.prevent="addEmptyClaim">{{ trans("general.add role") }} +</button>
        </div>

        <br><br>

        <div class="w-full px-3 mt-12">
            <button class="btn_save" type="submit">{{ trans("general.save") }}</button>
        </div>
    </form>
</div>
<script>

const claimsData = (initialClaims) => {
    return {
        claims: [],
        customClaimsEl: document.getElementById("customClaims"),
        customClaimsAddBtn: document.getElementById("customClaimsAddBtn"),
        init() {
            const keys = Object.keys(initialClaims || {});
            const values = Object.values(initialClaims || {});
            keys.forEach((key, index) => {
                this.claims.push({
                    key,
                    value: values[index]
                });
            });
        },
        addEmptyClaim() {
            this.claims.push({key: `custom_${parseInt(Math.random() * 100000)}`, value: ""});
        },
        removeClaim(ev) {
            this.claims = this.claims.filter(claim => {
                return claim.key !== ev.target.dataset.claimKey;
            });
        }
    }
}
</script>

</x-layouts.panel_layout>
