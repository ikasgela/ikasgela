<template>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-pencil-alt"></i> Cambiar la contraseña
        </div>
        <div class="card-body">
            <form class="form-horizontal">
                <div class="form-group row">
                    <label class="col-md-3">Contraseña actual</label>
                    <div class="col-md-9">
                        <input class="form-control"
                               :class="{'is-invalid': errors.current}" type="password"
                               v-model="password.current">
                        <span class="help-block">Tienes que escribir la contraseña actual para poder cambiarla.</span>
                        <div class="invalid-feedback" v-if="errors.current">
                            {{errors.current[0]}}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Nueva contraseña</label>
                    <div class="col-md-9">
                        <input class="form-control"
                               :class="{'is-invalid': errors.password}" type="password"
                               v-model="password.password">
                        <div class="invalid-feedback" v-if="errors.password">
                            {{errors.password[0]}}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Confirmar la contraseña</label>
                    <div class="col-md-9">
                        <input class="form-control"
                               :class="{'is-invalid': errors.password_confirmation}"
                               type="password" v-model="password.password_confirmation">
                        <div class="invalid-feedback" v-if="errors.password_confirmation">
                            {{errors.password_confirmation[0]}}
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="form-group row">
                <div class="col-md-9 offset-md-3">
                    <button class="btn btn-primary" type="button" :disabled="submiting"
                            @click="updateAuthUserPassword">
                        <i class="fas fa-spinner fa-spin" v-if="submiting"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                password: {},
                errors: {},
                submiting: false
            }
        },
        methods: {
            updateAuthUserPassword() {
                this.submiting = true
                axios.put(`/api/profile/updateAuthUserPassword`, this.password)
                    .then(response => {
                        this.password = {}
                        this.errors = {}
                        this.submiting = false
                        this.$toasted.global.error('¡Contraseña cambiada!');
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors
                        this.submiting = false
                    })
            }
        }
    }
</script>
