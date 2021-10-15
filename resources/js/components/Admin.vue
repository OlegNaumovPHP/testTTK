<template>
    <main>
        <div v-if="user.role_user === 'admin'" class="check_user_role">
            <header>
                <el-container class="el-container">
                    <el-header class="header">
                        <router-link :underline="false" to="/">Test</router-link>
                        <el-menu class="el__header">
                            <router-link class="nav__link" v-if="user.role_user === 'admin'" to="/admin">Админка</router-link>
                            <router-link :underline="false" class="nav__link" to="/">Главная</router-link>
                            <router-link v-if="token" class="nav__link" to="/logout">Выйти</router-link>
                            <router-link v-else class="nav__link" to="/login">Войти</router-link>
                        </el-menu>
                    </el-header>
                </el-container>
            </header>

            <section class="home_posts">
                <el-tooltip class="item" effect="light" content="Добавить автора" placement="top">
                    <el-button @click="dialogVisible=true" type="primary" class="el-icon-plus" circle style="margin: 0 -5px 0 0"></el-button>
                </el-tooltip>
                <el-alert
                    v-if="errored"
                    type="info"
                    center
                    :closable="false">
                    Произошла какая-то ошибка, попробуйте позже:(
                </el-alert>

                <div
                    class="loading__icons"
                    v-loading="loading"
                    element-loading-text="Загрузка..."
                    style="width: 100%; height: 50%; background: #000000ff">
                </div>

                <table v-if="loading === false">
                    <tr>
                        <th>№</th>
                        <th>Имя</th>
                        <th>Логин</th>
                        <th>Город</th>
                        <th>Комментарий</th>
                    </tr>
                    <tr v-for="(user, index) in users" :key="index">
                        <th>{{ user.id }}</th>
                        <td>{{ user.name }}</td>
                        <td>{{ user.login }}</td>
                        <td>{{ user.country }}</td>
                        <td style="max-width: 150px">{{ user.comment }}</td>
                        <td>
                            <el-tooltip class="item" effect="light" content="Редактировать автора" placement="top">
                                <el-button @click="dialogVisibleRedaction = true; editUser(user)" type="success" icon="el-icon-edit"></el-button>
                            </el-tooltip>
                        </td>
                    </tr>
                </table>
            </section>
            <!--Здаесь формочка для создания пользователя-->
            <el-dialog
                title="Новый пользователь"
                :visible.sync="dialogVisible"
                width="40%"
                :before-close="handleClose">
                <span>
                    <el-form class="no_border" enctype="multipart/form-data">
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
                        <div style="display: flex; justify-content: flex-end">
                            <el-button type="primary" :loading="load_state" @click.prevent="send(); getUser()">Добавить</el-button>
                            <el-button @click="dialogVisible=false">Отменить</el-button>
                        </div>
                    </el-form>
                </span>
            </el-dialog>

            <!--Здесь формочка для редактирования пользователя-->
            <el-dialog
                title="Редактирование пользователя"
                :visible="dialogVisibleRedaction"
                width="40%"
                :before-close="handleClose">
            <span>
                    <el-form class="no_border" enctype="multipart/form-data">
                        <valid_errors v-if="valid_errors" :errors="valid_errors"></valid_errors>
                        <el-form-item>
                            <el-input type="text" v-model="user.name" autofocus class="form-control" placeholder="Имя" />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="text" v-model="user.login" autofocus class="form-control" placeholder="Логин " />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="text" v-model="user.country" autofocus class="form-control" placeholder="Город рождения" />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="text" v-model="user.comment" autofocus class="form-control" placeholder="Комментарий" />
                        </el-form-item>
                        <div style="display: flex; justify-content: flex-end">
                            <el-button type="primary" :loading="load_state" @click.prevent="send(); getUser()">Изменить</el-button>
                            <el-button @click="dialogVisibleRedaction=false">Отменить</el-button>
                        </div>
                    </el-form>
                </span>
            </el-dialog>
        </div>
        <div v-else class="el-container">
            <el-alert
                style="display: none"
                v-if="errored"
                type="info"
                center
                :closable="false">
                Произошла какая-то ошибка, попробуйте позже:(
            </el-alert>

            <div
                class="loading__icons"
                v-loading="loading"
                element-loading-text="Загрузка..."
                style="display: none;">
            </div>
            <el-alert
                style="margin-top: calc(50vh - 250px);
                display:flex;
                justify-content: center;
                align-self: center;
                font-size: 500px;
                height: 500px"
                type="error"
                :closable="false">
                <h1>Зона для админа!</h1>
            </el-alert>
        </div>
    </main>
</template>

<script>
export default {
    data()
    {
        return {
            form_data: {
                name: '',
                login: '',
                country: '',
                comment: '',
                password: '',
            },
            dialogVisible: false,
            dialogVisibleRedaction: false,
            valid_errors: '',
            token: '',
            edit: false,
            loading: false,
            errored: false,
            load_state: false,
            user: {
                role_user: '',
                user_id: '',
                name: '',
                login: '',
                country: '',
                comment: '',
            },
            users: [],
            roles_user: [],
        }
    },
    async created() {
        const response = await axios.get('user', {
            headers: {
                Authorization: localStorage.getItem('token'),
            }
        });

        if (response.config.headers.Authorization) {
            this.token = response.config.headers.Authorization;
        }
    },
    mounted() {
        this.getUser();
        this.getRoleUser();
        this.user.role_user = localStorage.getItem('role');
        this.user.name = localStorage.getItem('name');
        this.user.login = localStorage.getItem('login');
        this.checkUserRole();
    },
    methods: {
        checkUserRole() {
            if (localStorage.getItem('role') !== 'admin') {
                setTimeout(() => {
                    this.$router.push({ path: '/' })
                }, 2000)
            }
        },
        getRoleUser() {
            axios
                .get('/getUserRole')
                .then(response => {
                    this.roles_user = response.data;
                })
                .catch(error => {
                    console.log(error)
                });
        },
        send() {
            let login = this.form_data.login;
            if (login === 'admin') {
                this.form_data.role_user = this.roles_user[1].id
            } else {
                this.form_data.role_user = this.roles_user[0].id
            }
            this.load_state = true;
            if (this.edit === false) {
                axios
                    .post('api/users', this.form_data)
                    .then(response => {
                        if (response.data.status === '422') {
                            this.$notify.error({
                                title: 'Ошибка!',
                                message: 'Error!',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.load_state = false;
                            }, 600);
                        } else {
                            this.$notify({
                                title: 'Успех!',
                                message: response.data.status,
                                type: 'success',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.load_state = false;
                                this.dialogVisible = false;
                            }, 600);
                        }
                        this.load_state = false;
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.valid_errors = error.response.data.errors;
                            this.$notify.error({
                                title: 'Ошибка!',
                                message: 'Проверьте праильность заполнения полей!',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.load_state = false;
                            }, 600);
                        }
                    })
                    .finally(() => {
                        this.form_data.name = '';
                        this.form_data.desc = '';
                        this.form_data.file = '';
                    })
                setTimeout(() => {
                    this.load_state = false;
                    this.getUser();
                }, 600);
            } else {
                axios
                    .put(`/api/users/${this.user.id}`, this.user)
                    .then(response => {
                        if (response.data.status === '422') {
                            this.valid_errors = error.response.data.errors;
                            this.$notify.error({
                                title: 'Ошибка!',
                                message: 'Error!',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.load_state = false;
                            }, 600);
                        } else {
                            this.$notify({
                                title: 'Успех!',
                                message: response.data.status,
                                type: 'success',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.load_state = false;
                                this.dialogVisibleRedaction = false;
                            }, 600);
                        }
                        this.load_state = false;
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.valid_errors = error.response.data.errors;
                            this.$notify.error({
                                title: 'Ошибка!',
                                message: 'Проверьте праильность заполнения полей!',
                                offset: 50
                            });
                            setTimeout(() => {
                                this.load_state = false;
                            }, 600);
                        }
                    })
                setTimeout(() => {
                    this.load_state = false;
                    this.edit = false;
                    this.getUser();
                }, 600);
            }
        },
        handleClose(done) {
            this.$confirm('Вы уверены, что хотите закрыть окно?')
                .then(_ => {
                    done();
                    this.dialogVisibleRedaction = false;
                })
                .catch(_ => {});
        },
        getUser() {
            let dontLoading = document.querySelector('.loading__icons');
            dontLoading.classList.remove('not__loading');
            this.loading = true;
            axios
                .get('/api/getUser')
                .then(response => {
                    this.users = response.data
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => setTimeout(() => {
                    this.loading = false
                    dontLoading.classList.add('not__loading');
                }, 500));
        },
        editUser(user) {
            this.edit = true
            this.user.id = user.id
            this.user.name = user.name
            this.user.login = user.login
            this.user.country = user.country
            this.user.comment = user.comment
            this.user.updated_at = user.updated_at
            this.user.created_at = user.created_at
        },
    }
}
</script>
