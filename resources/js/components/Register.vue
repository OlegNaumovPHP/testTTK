<template>
    <section>
        <header>
            <el-container class="el-container">
                <el-header class="header">
                    <router-link :underline="false" to="/">Test</router-link>
                    <el-menu class="el__header">
                        <router-link :underline="false" class="nav__link" to="/">Главная</router-link>
                        <router-link v-if="token" class="nav__link" to="/logout">Выйти</router-link>
                        <router-link v-else class="nav__link" to="/login">Войти</router-link>
                    </el-menu>
                </el-header>
            </el-container>
        </header>
        <div class="form__container">
            <el-form style="margin-top: 150px; background: #fff; padding: 24px; border-radius: 4px; box-shadow: 0 3px 10px 0 #22222255;">
                <h3 style="margin-bottom: 12px;">Регистрация</h3>
                <valid_errors v-if="valid_errors" :errors="valid_errors"></valid_errors>
                <el-form-item>
                    <el-input type="text" v-model="form_data.name" name="name" autofocus class="form-control" placeholder="Иванов Иван Иванович" />
                </el-form-item>
                <el-form-item>
                    <el-input type="text" v-model="form_data.login" name="login" class="form-control" placeholder="Ivan" />
                </el-form-item>
                <el-form-item>
                    <el-input type="text" v-model="form_data.country" name="login" class="form-control" placeholder="Страна рождения" />
                </el-form-item>
                <el-form-item>
                    <el-input type="textarea" style="border: none; padding: 0" v-model="form_data.comment" name="login" class="form-control" placeholder="Оставьте комментарий" />
                </el-form-item>
                <el-form-item>
                    <el-input type="password" v-model="form_data.password" name="password" class="form-control" placeholder="Введите пароль" show-password/>
                </el-form-item>
                <el-form-item>
                    <el-input type="password" v-model="form_data.password_confirm" name="password_confirm" class="form-control" placeholder="Повторите пароль" show-password/>
                </el-form-item>
                <el-button :loading="loadState" @click.prevent='send' style="outline: none">Зарегистрироваться</el-button>
                <router-link class="log-reg" to="/login">Есть аккаунта? - Авторизируйтесь!</router-link>
            </el-form>
        </div>
    </section>
</template>

<script>
export default {
    data()
    {
        return {
            token: '',
            loadState: false,
            valid_errors: '',
            form_data: {
                name: '',
                login: '',
                country: '',
                comment: '',
                password: '',
                password_confirm: '',
                role_user: '',
            },
            admin: {
                name: 'admin',
                login: 'admin',
                country: 'admin',
                comment: 'admin',
                password: '123123123',
                password_confirm: '123123123',
                role_user: '',
            },
            roles_user: [],
        }
    },
    mounted() {
        this.getRoleUser();
        this.regRoles();
    },
    methods:
        {
            checkUser() {
                axios
                    .get('/api/getUser')
                    .then(response => {
                        this.users = response.data
                        if (response.data.length < 1) {
                            this.regAdmin();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            regAdmin() {
                this.admin.role_user = this.roles_user[1].id
                axios
                    .post('api/users', this.admin)
            },
            getRoleUser() {
                axios
                    .get('/getUserRole')
                    .then(response => {
                        this.roles_user = response.data;
                        this.checkUser();
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            regRoles() {
                axios
                    .post('regRoles', '123')
            },
            send() {
                this.form_data.role_user = this.roles_user[0].id
                this.loadState = true
                axios
                    .post('/register', this.form_data)
                    .then(response => {
                        this.$notify({
                            title: 'Успех!',
                            message: response.data.status,
                            type: 'success',
                            offset: 50
                        });
                        setTimeout(() => {
                            this.loadState = false;
                            this.redirect();
                        }, 600);
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.valid_errors = error.response.data.errors;
                            this.$notify.error({
                                title: 'Ошибка!',
                                message: 'Проверьте правильность заполнения полей!',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.loadState = false;
                            }, 600);
                        }

                    })
            },
            redirect() {
                this.$router.push({ path: '/login' })
            }
        }
}
</script>
