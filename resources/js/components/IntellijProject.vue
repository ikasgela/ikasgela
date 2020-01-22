<template>
    <button class="btn btn-warning" type="button" :disabled="submiting"
            @click="updateAuthUserPassword">
        <i class="fas fa-spinner fa-spin" v-if="submiting"></i> Clonar el proyecto
    </button>

</template>

<script>
    export default {
        name: 'intellij-project',
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
