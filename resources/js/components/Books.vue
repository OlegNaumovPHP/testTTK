<template>
    <main>
        <section class="home_posts">
            <h2 style="margin-bottom: 16px">{{ name_section }}</h2>
            <!--        Здесь фильтр-->
            <div class="filter_posts">
                <el-tooltip class="item" effect="light" content="Назад" placement="top">
                    <el-button @click="back()" class="el-icon-back" circle></el-button>
                </el-tooltip>
                <div v-if="token !== ''" class="buttons">
                    <el-tooltip class="item" effect="light" content="Добавить зпись" placement="top">
                        <el-button @click="dialogVisible=true" type="primary" class="el-icon-plus" circle style="margin: 0 -5px 0 0"></el-button>
                    </el-tooltip>
                </div>
                <el-tooltip class="item" effect="light" content="Перезагрузить" placement="top">
                    <el-button class="el-icon-refresh" @click="getBooks()" circle></el-button>
                </el-tooltip>
                <el-input
                    style="width: 240px;"
                    placeholder="Поиск по описанию..."
                    auto-complete="on"
                    v-model="desc_search">
                    <i slot="prefix" class="el-input__icon el-icon-search"></i>
                </el-input>
                <el-input
                    style="width: 240px;"
                    placeholder="Поиск по названию..."
                    auto-complete="on"
                    v-model="name_search">
                    <i slot="prefix" class="el-input__icon el-icon-search"></i>
                </el-input>
                <el-input
                    v-model="user_search"
                    filterable
                    placeholder="Поиск во автору..."
                    style="width: 240px"
                    auto-complete="on">
                    <i slot="prefix" class="el-input__icon el-icon-search" />
                </el-input>
            </div>

            <!--Здесь формочка для редактирования записи-->
            <el-dialog
                title="Редактирование книги"
                :visible="dialogVisibleRedaction"
                width="40%"
                :before-close="handleClose">
            <span>
                    <el-form class="no_border" enctype="multipart/form-data">
                        <valid_errors v-if="valid_errors" :errors="valid_errors"></valid_errors>
                        <el-alert
                            v-if="validErrors"
                            style="margin-bottom: 16px"
                            type="error"
                            :closable="false">
                            <p v-for="error in errors">
                                {{ error }}
                            </p>
                        </el-alert>
                        <el-form-item>
                            <el-input type="text" v-model="book.name" autofocus class="form-control" placeholder="Название книги" maxlength="40" show-word-limit />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="text" v-model="book.year" autofocus class="form-control" placeholder="Год издания" maxlength="4" show-word-limit />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="textarea" style="border: none; padding: 0" v-model="book.desc" autofocus class="form-control" placeholder="Описание книги" maxlength="500" show-word-limit />
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
                            <div slot="tip" class="el-upload__tip">файлы jpg размером менее 500 КБ</div>
                        </el-upload>
                        <div style="display: flex; justify-content: flex-end">
                            <el-button type="primary" :loading="load_state" @click.prevent="validRedact">Изменить</el-button>
                            <el-button @click="dialogVisibleRedaction=false">Отменить</el-button>
                        </div>
                    </el-form>
                </span>
            </el-dialog>


            <!--Здаесь формочка для создания записи-->
            <el-dialog
                title="Новая книга"
                :visible.sync="dialogVisible"
                width="40%"
                :before-close="handleClose">
                <span>
                    <el-form class="no_border" enctype="multipart/form-data">
                        <valid_errors v-if="valid_errors" :errors="valid_errors"></valid_errors>
                        <el-alert
                            v-if="validErrors"
                            style="margin-bottom: 16px"
                            type="error"
                            :closable="false">
                            <p v-for="error in errors">
                                {{ error }}
                            </p>
                        </el-alert>
                        <el-form-item>
                            <el-input type="text" v-model="form_data.name" autofocus class="form-control" placeholder="Название книги" maxlength="40" show-word-limit />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="text" v-model="form_data.year" autofocus class="form-control" placeholder="Год издания" maxlength="4" show-word-limit />
                        </el-form-item>
                        <el-form-item>
                            <el-input type="textarea" style="border: none; padding: 0" v-model="form_data.desc" autofocus class="form-control" placeholder="Описание книги" maxlength="500" show-word-limit />
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
                            <div slot="tip" class="el-upload__tip">файлы jpg размером менее 500 КБ</div>
                        </el-upload>
                        <div style="display: flex; justify-content: flex-end">
                            <el-button type="primary" :loading="load_state" @click.prevent="valid">Добавить</el-button>
                            <el-button @click="dialogVisible=false">Отменить</el-button>
                        </div>
                    </el-form>
                </span>
            </el-dialog>


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

            <div>
                <table v-if="loading === false">
                    <tr>
                        <th>№</th>
                        <th>Название</th>
                        <th>Превью</th>
                        <th>Год выпуска</th>
                        <th>Описание</th>
                        <th v-if="book.user_id === user.user_id || user.role_user === 'admin'">Действия</th>
                        <th v-else>Действие</th>
                    </tr>
                    <tr v-if="book.soft_delete === 'no' && book.section_id == numberSection" v-for="(book, index) in filteredBooks" :key="index" v-model="book.name">
                        <th>{{ book.id }}</th>
                        <td style="max-width: 150px">{{ book.name }}</td>
                        <td>
                            <el-tooltip class="item" effect="light" content="Увеличить фото" placement="top">
                                <el-image
                                    class="books__file"
                                    :src="book.file"
                                    @click="visibleFile = true; srcModal = book.file">
                                </el-image>
                            </el-tooltip>
                        </td>
                        <td>{{ book.year + ' г.'}}</td>
                        <td style="max-width: 150px">{{ book.desc }}</td>
                        <td v-if="book.user_id === user.user_id || user.role_user === 'admin'" style="width: 300px">
                            <el-tooltip class="item" effect="light" content="Редактировать книгу" placement="top">
                                <el-button @click="dialogVisibleRedaction = true; editBook(book)" type="success" icon="el-icon-edit"></el-button>
                            </el-tooltip>
                            <el-tooltip v-if="user.role_user === 'admin'" class="item" effect="light" content="Удалить книгу" placement="top">
                                <el-button @click="deleteBook(book.id)" type="danger" icon="el-icon-delete"></el-button>
                            </el-tooltip>
                            <el-tooltip v-if="user.role_user === 'admin'" class="item" effect="light" content="Скрыть книгу" placement="top">
                                <el-button @click="book.soft_delete = 'yes'; editBook(book); softDelete()" icon="el-icon-view"></el-button>
                            </el-tooltip>
                        </td>
                        <td v-else>
                            <el-tooltip class="item" effect="light" content="Показать побльше" placement="top">
                                <el-button @click="watchBook(book.id); visibleShowBook = true" icon="el-icon-search"></el-button>
                            </el-tooltip>
                        </td>
                    </tr>
                    <tr v-else-if="user.role_user === 'admin' && book.section_id == numberSection">
                        <th>{{ book.id }}</th>
                        <td>{{ book.name }}</td>
                        <td>
                            <el-tooltip class="item" effect="light" content="Увеличить фото" placement="top">
                                <el-image
                                    class="books__file"
                                    :src="book.file"
                                    @click="visibleFile = true; srcModal = book.file">
                                </el-image>
                            </el-tooltip>
                        </td>
                        <td>{{ book.year }}</td>
                        <td>{{ book.desc }}</td>
                        <td>
                            <el-tooltip class="item" effect="light" content="Проявить книгу" placement="top">
                                <el-button @click="book.soft_delete = 'no'; editBook(book);  softDelete();" type="success">Восстановить</el-button>
                            </el-tooltip>
                        </td>
                    </tr>
                </table>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <el-button v-if="disabledPrev" disabled class="page-link prev"aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </el-button>
                        <el-button v-else @click.prevent="getBooks(pagination.prev_page_url)"  class="page-link prev" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </el-button>
                    </li>
                    <li class="page-item">
                        <el-button disabled style="cursor: default; color: #000" class="page-link">
                            Страница {{ pagination.current_page }} из {{ pagination.last_page }}
                        </el-button>
                    </li>
                    <li class="page-item">
                        <el-button v-if="disabledNext" disabled class="page-link next" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </el-button>
                        <el-button v-else @click.prevent="getBooks(pagination.next_page_url)" class="page-link next" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </el-button>
                    </li>
                </ul>
            </nav>
            <el-dialog
                :visible.sync="visibleFile"
                width="30%">
                <el-image
                    style="width: 100%"
                    :src="srcModal"
                    @click="visibleFile = true">
                </el-image>
            </el-dialog>
            <el-dialog
                :visible.sync="visibleShowBook">
                <div
                    class="form_block"
                    style="display: flex;
                    justify-content: space-between">
                    <el-image
                        style="width: 45%"
                        :src="showBook.file"
                        @click="visibleFile = true">
                    </el-image>
                    <div
                        class="info"
                        style="width: 45%">
                        Название: <el-input v-model="showBook.name" type="text" disabled />
                        Описание: <el-input v-model="showBook.desc" type="textarea" disabled />
                        Год выпуска: <el-input v-model="showBook.year" type="text" disabled />
                        Дата создания: <el-input v-model="showBook.created_at" type="text" disabled />
                        Дата изменения: <el-input v-model="showBook.updated_at" type="text" disabled />
                    </div>
                </div>
            </el-dialog>
        </section>
    </main>
</template>

<script>
export default {
    data()
    {
        return {
            visibleShowBook: false,
            numberSection: '',
            srcModal: '',
            visibleFile: false,
            token: '',
            books: [],
            book: {
                id: '',
                name: '',
                user_id: '',
                user_name: '',
                year: '',
                desc: '',
                file: '',
                created_at: '',
                updated_at: '',
                soft_delete: 'no',
            },
            pagination: {},
            edit: false,
            loading: false,
            errored: false,
            valid_errors: '',
            form_data: {
                name: '',
                year: '',
                desc: '',
                file: '',
                user_id: ''
            },
            showBook: {
                name: '',
                desc: '',
                file: '',
                year: '',
                created_at: '',
                updated_at: '',
            },
            month: {
                0: 'января',
                1: 'февраля',
                2: 'марта',
                3: 'апреля',
                4: 'мая',
                5: 'июня',
                6: 'июля',
                7: 'августа',
                8: 'сентября',
                9: 'октября',
                10: 'ноября',
                11: 'декабря',
            },
            uploadFile: '',
            fileName: '',
            name_section: '',
            load_state: false,
            name_search: '',
            desc_search: '',
            user_search: '',
            dialogVisible: false,
            dialogVisibleRedaction: false,
            user: {
                role_user: '',
                user_id: '',
                name: '',
                login: ''
            },
            users: [],
            disabledPrev: false,
            disabledNext: false,
            errors: [],
            validErrors: false
        }
    },
    computed: {
        filteredBooks() {
            if (this.name_search) {
                return this.books.filter(book => {
                    return book.name.toLowerCase().indexOf(this.name_search.toLowerCase()) !== -1;
                })
            } else if (this.desc_search) {
                return this.books.filter(book => {
                    return book.desc.toLowerCase().indexOf(this.desc_search.toLowerCase()) !== -1;
                })
            } else if (this.user_search) {
                return this.books.filter(book => {
                    return book.user_name.toLowerCase().indexOf(this.user_search.toLowerCase()) !== -1;
                })
            } else {
                return this.books;
            }
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

        this.numberSection = localStorage.getItem('numberSection');
    },
    mounted() {
        this.getBooks();
        this.getUser();
        this.form_data.user_id = localStorage.getItem('user_id');
        this.form_data.user_name = localStorage.getItem('name');
        this.user.user_id = localStorage.getItem('user_id');
        this.user.role_user = localStorage.getItem('role');
        this.user.name = localStorage.getItem('name');
        this.user.login = localStorage.getItem('login');
        this.form_data.section_id = localStorage.getItem('numberSection');
        this.name_section = localStorage.getItem('nameSection');
        console.log(localStorage.getItem('nameSection'))
    },
    methods: {
        valid() {
            this.$refs.upload.submit();
            if (this.form_data.name.length > 3 && this.form_data.desc.length > 8 && this.form_data.year.length > 3 && this.form_data.file) {
                this.send();
                this.getBooks();
                this.validErrors = false;
            } else if (this.form_data.name.length < 3) {
                this.errors.pop()
                this.errors.push('Минимальная длина названия: 3');
                this.validErrors = true;
            } else if (this.form_data.year.length < 3) {
                this.errors.pop()
                this.errors.push('Минимальная длина года издания: 3');
                this.validErrors = true;
            } else if (this.form_data.desc.length < 8) {
                this.errors.pop()
                this.errors.push('Минимальная длина описания: 8');
                this.validErrors = true;
            } else if (!this.form_data.file) {
                this.errors.pop()
                this.errors.push('Изображение обязательно!');
                this.validErrors = true;
            }
        },
        validRedact() {
            if (this.book.name.length > 3 && this.book.desc.length > 8 && this.book.year.length > 3 && this.book.file) {
                this.send();
                this.getBooks();
                this.validErrors = false;
            } else if (this.book.name.length < 3) {
                this.errors.pop()
                this.errors.push('Минимальная длина названия: 3');
                this.validErrors = true;
            } else if (this.book.year.length < 3) {
                this.errors.pop()
                this.errors.push('Минимальная длина года издания: 3');
                this.validErrors = true;
            } else if (this.book.desc.length < 8) {
                this.errors.pop()
                this.errors.push('Минимальная длина описания: 8');
                this.validErrors = true;
            }
        },
        back() {
            localStorage.setItem('numberSection', '');
            localStorage.setItem('NameSection', '');
            this.$router.push({ path: '/' });
        },
        getBooks(page_url) {
            page_url = page_url || `/api/section/${localStorage.getItem('numberSection')}`
            let dontLoading = document.querySelector('.loading__icons');
            dontLoading.classList.remove('not__loading');
            this.loading = true;
            axios
                .get(page_url)
                .then(response => {
                    this.books = response.data.data;
                    this.makePagination(response.data);
                    if (this.books.length >= 0) {
                        for (let i = 0; i < this.books.length; i++) {

                            let updatedBookDate = this.books[i].updated_at;
                            let updatedDate = new Date(updatedBookDate);
                            let updatedHours = updatedDate.getHours();
                            let updatedMinutes = updatedDate.getMinutes();
                            let updatedMonth = this.month[updatedDate.getMonth()];
                            let updatedDay = updatedDate.getDate();

                            if (updatedHours < 10) {
                                updatedHours = '0' + updatedHours;
                            }
                            if (updatedMinutes < 10) {
                                updatedMinutes = '0' + updatedMinutes;
                            }

                            this.books[i].updated_at = updatedDay + ' ' + updatedMonth + ' в ' + updatedHours + ':' + updatedMinutes;


                            let createdBookDate = this.books[i].created_at;
                            let createdDate = new Date(createdBookDate);
                            let createdHours = createdDate.getHours();
                            let createdMinutes = createdDate.getMinutes();
                            let createdMonth = this.month[updatedDate.getMonth()];
                            let createdDay = createdDate.getDate();

                            if (createdHours < 10) {
                                createdHours = '0' + createdHours;
                            }
                            if (createdMinutes < 10) {
                                createdMinutes = '0' + createdMinutes;
                            }

                            this.books[i].created_at = createdDay + ' ' + createdMonth + ' в ' + createdHours + ':' + createdMinutes;
                        }
                    }
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true;
                })
                .finally(() => setTimeout(() => {
                    this.loading = false
                    dontLoading.classList.add('not__loading');
                }, 500));
        },
        makePagination(response) {
            let pagination = {
                current_page: response.current_page,
                last_page: response.last_page,
                prev_page_url: response.prev_page_url,
                next_page_url: response.next_page_url,
            }

            if (!pagination.prev_page_url) {
                this.disabledPrev = true
            } else {
                this.disabledPrev = false
            }

            if (!pagination.next_page_url) {
                this.disabledNext = true
            } else {
                this.disabledNext = false
            }

            this.pagination = pagination;
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
        handleClose(done) {
            this.$confirm('Вы уверены, что хотите закрыть окно?')
                .then(_ => {
                    done();
                    this.dialogVisibleRedaction = false;
                })
                .catch(_ => {});
        },
        send() {
            this.load_state = true;
            if (this.edit === false) {
                axios
                    .post('api/books', this.form_data)
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
                        this.form_data.year = '';
                    })
                setTimeout(() => {
                    this.load_state = false;
                    this.getBooks();
                }, 600);
            } else {
                axios
                    .put(`/api/books/${this.book.id}`, this.book)
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
                    this.getBooks();
                }, 600);
            }
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
        deleteBook(id) {
            axios
                .delete(`/api/books/${id}`)
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

            this.getBooks();
            this.getBooks();
        },
        watchBook(id) {
            axios
                .get(`/api/books/${id}`)
                .then(response => {
                    this.showBook = response.data

                    let updatedBookDate = response.data.updated_at;
                    let updatedDate = new Date(updatedBookDate);
                    let updatedHours = updatedDate.getHours();
                    let updatedMinutes = updatedDate.getMinutes();
                    let updatedMonth = this.month[updatedDate.getMonth()];
                    let updatedDay = updatedDate.getDate();

                    if (updatedHours < 10) {
                        updatedHours = '0' + updatedHours;
                    }
                    if (updatedMinutes < 10) {
                        updatedMinutes = '0' + updatedMinutes;
                    }

                    this.showBook.updated_at = updatedDay + ' ' + updatedMonth + ' в ' + updatedHours + ':' + updatedMinutes;


                    let createdBookDate = response.data.created_at;
                    let createdDate = new Date(createdBookDate);
                    let createdHours = createdDate.getHours();
                    let createdMinutes = createdDate.getMinutes();
                    let createdMonth = this.month[updatedDate.getMonth()];
                    let createdDay = createdDate.getDate();

                    if (createdHours < 10) {
                        createdHours = '0' + createdHours;
                    }
                    if (createdMinutes < 10) {
                        createdMinutes = '0' + createdMinutes;
                    }

                    this.showBook.created_at = createdDay + ' ' + createdMonth + ' в ' + createdHours + ':' + createdMinutes;
                })
                .catch(error => {
                    console.log(error)
                })
        },
        editBook(book) {
            this.edit = true
            this.book.id = book.id
            this.book.user_id = book.user_id
            this.book.name = book.name
            this.book.desc = book.desc
            this.book.file = book.file
            this.book.year = book.year
            this.book.soft_delete = book.soft_delete
            this.book.updated_at = book.updated_at
            this.book.created_at = book.created_at
        },
        softDelete() {
            axios
                .put(`/api/books/${this.book.id}`, this.book)
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
                            message: 'Запись скрыта/проявлена!',
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
