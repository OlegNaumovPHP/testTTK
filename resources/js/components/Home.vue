<template>
    <main>
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

            <el-row style="width: 100%; margin-bottom: 16px">
                <el-tooltip v-if="user.role_user === 'admin'" class="item" effect="light" content="Добавить раздел" placement="top">
                    <el-button @click="dialogVisible=true" type="primary" class="el-icon-plus" style="margin: 0 -5px 0 0" circle></el-button>
                </el-tooltip>
                <el-tooltip class="item" effect="light" content="Перезагрузить" placement="top">
                    <el-button class="el-icon-refresh" @click="getSections()" circle></el-button>
                </el-tooltip>
            </el-row>

            <el-alert
                v-if="errored || sections.length < 1"
                type="info"
                center
                :closable="false">
                Произошла какая-то ошибка(возможно разделов пока что не существует), попробуйте позже :(
            </el-alert>

            <div
                class="loading__icons"
                v-loading="loading"
                element-loading-text="Загрузка..."
                style="width: 100%; height: 50%; background: #000000ff">
            </div>


            <div v-if="loading === false">
                <div v-if="section.soft_delete === 'no'" v-for="section in sections" >
                    <div v-if="user.role_user === 'admin'"  style="float: right; background: #fff; box-shadow: 0 3px 10px 0 #22222255; padding: 10px; border-radius: 4px">
                        <el-tooltip class="item" effect="light" content="Редактировать раздел" placement="top">
                            <el-button @click="dialogVisibleRedaction = true; editSection(section)" type="success" icon="el-icon-edit"></el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="light" content="Удалить раздел" placement="top">
                            <el-button @click="deleteSection(section.id)"  type="danger" icon="el-icon-delete"></el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="light" content="Скрыть раздел" placement="top">
                            <el-button @click="section.soft_delete = 'yes'; editSection(section); softDelete()" icon="el-icon-view"></el-button>
                        </el-tooltip>
                    </div>
                    <button class="sections" @click="numberSection = section.id; sectionBooks()">
                        <el-row :gutter="24">
                            <el-col :xl="18">
                                <h2>{{ section.name }}</h2>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col style="padding: 0">
                                <el-image
                                    :src=section.file
                                    style="border-radius: 4px;
                                    display:flex;
                                    justify-content:center;
                                    align-items: center;
                                    border: none;
                                    max-height: 500px;
                                    width: 100%;
                                    :fit=cover">
                                </el-image>
                            </el-col>
                        </el-row>
                        <el-row style="margin-top: 12px">
                            <p>{{ section.desc }}</p>
                        </el-row>
                    </button>
                </div>

                <div v-if="section.soft_delete === 'yes' && user.role_user === 'admin'" v-for="section in sections" >
                    <div v-if="user.role_user === 'admin'"  style="float: right; background: #fff; box-shadow: 0 3px 10px 0 #22222255; padding: 10px; border-radius: 4px">
                        <el-tooltip class="item" effect="light" content="Проявить раздел" placement="top">
                            <el-button @click="section.soft_delete = 'no'; editSection(section);  softDelete();" type="success">Восстановить</el-button>
                        </el-tooltip>
                    </div>
                    <button class="sections" @click="numberSection = section.id; sectionBooks()">
                        <el-row :gutter="24">
                            <el-col :xl="18">
                                <h2>{{ section.name }}</h2>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col style="padding: 0">
                                <el-image
                                    :src=section.file
                                    style="border-radius: 4px;
                                    display:flex;
                                    justify-content:center;
                                    align-items: center;
                                    border: none;
                                    max-height: 500px;
                                    width: 100%;
                                :fit=cover">
                                </el-image>
                            </el-col>
                        </el-row>
                        <el-row style="margin-top: 12px">
                            <p>{{ section.desc }}</p>
                        </el-row>
                    </button>
                </div>

            </div>
        </section>

        <!--Здаесь формочка для создания записи-->
        <el-dialog
            title="Новый раздел"
            :visible.sync="dialogVisible"
            width="40%"
            :before-close="handleClose">
                <span>
                    <el-form class="no_border" enctype="multipart/form-data">
                        <valid_errors v-if="valid_errors" :errors="valid_errors"></valid_errors>
                        <el-form-item>
                            <el-input type="text" v-model="form_data.name" autofocus class="form-control" placeholder="Название раздела" />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="textarea" style="border: none; padding: 0" v-model="form_data.desc" autofocus class="form-control" placeholder="Описание раздела" />
                        </el-form-item>
                        <el-upload
                            ref="upload"
                            action="api/upload-file"
                            list-type="fileList"
                            :limit="1"
                            :accept="'image/.jpeg,.jpg,.png'"
                            :auto-upload="false"
                            :on-remove="handleRemove"
                            :before-upload="upload_file"
                            :on-success="success_file">
                            <el-button size="small" type="primary">Нажмите, чтобы загрузить</el-button>
                            <div slot="tip" class="el-upload__tip">файлы jpg/png размером менее 500 КБ</div>
                        </el-upload>
                        <div style="display: flex; justify-content: flex-end">
                            <el-button type="primary" :loading="load_state" @click.prevent="send(); getSections()">Добавить</el-button>
                            <el-button @click="dialogVisible=false">Отменить</el-button>
                        </div>
                    </el-form>
                </span>
        </el-dialog>

        <!--Здесь формочка для редактирования записи-->
        <el-dialog
            title="Редактирование раздела"
            :visible="dialogVisibleRedaction"
            width="40%"
            :before-close="handleClose">
            <span>
                    <el-form class="no_border" enctype="multipart/form-data">
                        <valid_errors v-if="valid_errors" :errors="valid_errors"></valid_errors>
                        <el-form-item>
                            <el-input type="text" v-model="section.name" autofocus class="form-control" placeholder="Название раздела" />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="textarea" style="border: none; padding: 0" v-model="section.desc" autofocus class="form-control" placeholder="Описание раздела" />
                        </el-form-item>
                        <el-upload
                            ref="upload"
                            action="api/upload-file"
                            list-type="fileList"
                            :limit="1"
                            :accept="'image/.jpeg,.jpg,.png'"
                            :auto-upload="false"
                            :on-remove="handleRemove"
                            :before-upload="upload_file"
                            :on-success="success_file">
                            <el-button size="small" type="primary">Нажмите, чтобы загрузить</el-button>
                            <div slot="tip" class="el-upload__tip">файлы jpg/png размером менее 500 КБ</div>
                        </el-upload>
                        <div style="display: flex; justify-content: flex-end">
                            <el-button type="primary" :loading="load_state" @click.prevent="send(); getSections()">Изменить</el-button>
                            <el-button @click="dialogVisibleRedaction=false">Отменить</el-button>
                        </div>
                    </el-form>
                </span>
        </el-dialog>
    </main>
</template>

<script>
export default {
    data()
    {
        return {
            numberSection: '',
            token: '',
            edit: false,
            loading: false,
            errored: false,
            sections: [],
            section: {
                name: '',
                desc: '',
                file: '',
                soft_delete: 'no'
            },
            valid_errors: '',
            form_data: {
                name: '',
                desc: '',
                file: ''
            },
            load_state: false,
            user: {
                role_user: '',
                user_id: '',
                name: '',
                login: ''
            },
            users: [],
            dialogVisible: false,
            dialogVisibleRedaction: false,
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
        this.getSections();
        this.user.user_id = localStorage.getItem('user_id');
        this.user.role_user = localStorage.getItem('role');
        this.user.name = localStorage.getItem('name');
        this.user.login = localStorage.getItem('login');
    },
    methods: {
        send() {
            this.$refs.upload.submit();
            this.load_state = true;
            if (this.edit === false) {
                axios
                    .post('api/section', this.form_data)
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
                    this.getSections();
                }, 600);
            } else {
                axios
                    .put(`/api/section/${this.section.id}`, this.section)
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
                    this.getSections();
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
        sectionBooks() {
            localStorage.setItem('numberSection', this.numberSection);
            this.$router.push({ path: '/books' });
        },
        getSections() {
            let dontLoading = document.querySelector('.loading__icons');
            dontLoading.classList.remove('not__loading');
            this.loading = true;
            axios
                .get('/api/section')
                .then(response => {
                    this.sections = response.data
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => setTimeout(() => {
                    this.loading = false
                    dontLoading.classList.add('not__loading');
                }, 500));
        },
        getUser() {
            axios
                .get('/api/getUser')
                .then(response => {
                    this.users = response.data
                })
                .catch(error => {
                    console.log(error);
                })
        },
        handleRemove(file) {
            this.imageUrl = '';
        },
        success_file(res, file) {
            this.form_data.file = file.name;
        },
        upload_file(file) {
            this.form_data.file = file.name;
        },
        deleteSection(id) {
            axios
                .delete(`/api/section/${id}`)
                .then(response => {
                    if (response.data.status === '422') {
                        this.$notify.error({
                            title: 'Ошибка!',
                            message: 'Error!',
                            offset: 50
                        });
                    } else {
                        this.$notify({
                            title: 'Успех!',
                            message: response.data.status,
                            type: 'success',
                            offset: 50
                        });
                    }
                })
                .catch(error => {
                    this.valid_errors = error.response.data.errors;
                    this.$notify.error({
                        title: 'Ошибка!',
                        message: 'Удалить запись не удалось!',
                        offset: 50
                    });
                });

            this.getSections();
            this.getSections();
        },
        editSection(section) {
            this.edit = true
            this.section.id = section.id
            this.section.name = section.name
            this.section.desc = section.desc
            this.section.file = section.file
            this.section.soft_delete = section.soft_delete
            this.section.updated_at = section.updated_at
            this.section.created_at = section.created_at
        },
        softDelete() {
            axios
                .put(`/api/section/${this.section.id}`, this.section)
                .then(response => {
                    if (response.data.status === '422') {
                        this.valid_errors = error.response.data.errors;
                        this.$notify.error({
                            title: 'Ошибка!',
                            message: 'Не удалось скрыть запись!',
                            offset: 50
                        });
                        setTimeout(() => {
                            this.load_state = false;
                        }, 600);
                    } else {
                        this.$notify({
                            title: 'Успех!',
                            message: 'Раздел скрыт/проявлен!',
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
                            message: 'Не удалось скрыть запись!',
                            offset: 50
                        });
                        setTimeout(() => {
                            this.load_state = false;
                        }, 600);
                    }
                })
        }
    }
}
</script>
