<template>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-pencil-alt"></i> Editar perfil
        </div>
        <div class="card-body">
            <form class="form-horizontal">
                <div class="form-group row">
                    <label class="col-md-3">Nombre completo</label>
                    <div class="col-md-9">
                        <input class="form-control" :class="{'is-invalid': errors.name}"
                               type="text" v-model="user.name">
                        <span class="help-block">Escribe tu nombre para que la gente pueda reconocerte.</span>
                        <div class="invalid-feedback" v-if="errors.name">
                            {{errors.name[0]}}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3">Email</label>
                    <div class="col-md-9">
                        <input class="form-control" :class="{'is-invalid': errors.email}"
                               type="email" v-model="user.email" readonly>
                        <span class="help-block">Esta dirección de email se mostrará en tu perfil público.</span>
                        <div class="invalid-feedback" v-if="errors.email">
                            {{errors.email[0]}}
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="form-group row">
                <div class="col-md-9 offset-md-3">
                    <button class="btn btn-primary" type="button" :disabled="submiting" @click="updateAuthUser">
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
                user: {},
                errors: {},
                submiting: false
            }
        },
        mounted() {
            this.getAuthUser()
        },
        methods: {
            getAuthUser() {
                axios.get(`/api/profile/getAuthUser`)
                    .then(response => {
                        this.user = response.data
                    })
            },
            updateAuthUser() {
                this.submiting = true
                axios.put(`/api/profile/updateAuthUser`, this.user)
                    .then(response => {
                        this.errors = {}
                        this.submiting = false
                        this.$toasted.global.error('¡Perfil actualizado!');
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors
                        this.submiting = false
                    })
            }
        }
    }
</script>
