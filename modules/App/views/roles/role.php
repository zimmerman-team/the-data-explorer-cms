<?php

    $permissions = new ArrayObject([
        'Users' => [
            'app.users.manage' => 'Manage users',
            'app.roles.manage' => 'Manage roles',
        ]
    ]);

    $this->trigger('app.permissions.collect', [$permissions]);


?>
<kiss-container class="kiss-margin-large" size="small">

    <vue-view>
        <template>

            <h1 class="kiss-margin-large-bottom">
                <span v-if="!role._id"><?=_t('Create role')?></span>
                <span v-if="role._id"><?=_t('Edit role')?></span>
            </h1>

            <form :class="{'kiss-disabled':saving}" @submit.prevent="save">

                <div class="kiss-margin" :class="{'kiss-disabled': role._id}">
                    <label><?=_t('APPID')?></label>
                    <input class="kiss-input" type="text" pattern="[a-zA-Z0-9_]+" v-model="role.appid" :disabled="role._id" required>
                </div>

                <div class="kiss-margin">
                    <label><?=_t('Name')?></label>
                    <input class="kiss-input" type="text" v-model="role.name" required>
                </div>

                <div class="kiss-margin">
                    <label><?=_t('Info')?></label>
                    <textarea class="kiss-input kiss-textarea" style="height:150px;" v-model="role.info"></textarea>
                </div>

                <div class="kiss-margin kiss-margin-large-top kiss-size-4"><strong><?=_t('Permissions')?></strong></div>

                <div class="kiss-margin" v-for="(permissions, group) in permissions">

                    <strong class="kiss-text-caption">{{ group }}</strong>

                    <div class="kiss-margin-small kiss-size-small kiss-flex kiss-middle" v-for="(label, permission) in permissions">
                        <div class="kiss-flex-1 kiss-margin-small-right">
                            <div :class="{'kiss-color-muted':!role.permissions[permission]}">
                                {{label}}
                            </div>
                            <div class="kiss-text-monospace kiss-color-muted">{{ permission }}</div>
                        </div>
                        <div><field-boolean v-model="role.permissions[permission]"></field-boolean></div>
                    </div>

                </div>


                <div class="kiss-margin-large kiss-flex kiss-flex-middle">
                    <button type="submit" class="kiss-button kiss-button-primary">
                        <span v-if="!role._id"><?=_t('Create role')?></span>
                        <span v-if="role._id"><?=_t('Update role')?></span>
                    </button>
                    <a class="kiss-margin-left kiss-button kiss-button-link" href="<?=$this->route('/users/roles')?>">
                        <span v-if="!role._id"><?=_t('Cancel')?></span>
                        <span v-if="role._id"><?=_t('Close')?></span>
                    </a>
                </div>

            </form>

        </template>

        <script type="module">

            export default {
                data() {

                    return {
                        saving: false,
                        role: <?=json_encode($role)?>,
                        permissions: <?=json_encode($permissions)?>
                    };
                },

                methods: {

                    save() {

                        let isUpdate = this.role._id;

                        this.saving = true;

                        App.request('/users/roles/save', {role: this.role}).then(role => {
                            
                            this.role = role;
                            this.saving = false;

                            if (isUpdate) {
                                App.ui.notify('Role updated!');
                            } else {
                                App.ui.notify('Role created!');
                            }
                        }).catch(res => {
                            this.saving = false;
                            App.ui.notify(res.error || 'Saving failed!', 'error');
                        })

                    }
                }
            }
        </script>

    </vue-view>

</kiss-container>