<template>
    <div id="app">
        <Header />
            <router-view />
        <Footer />
    </div>
</template>

<script>

export default {
    comments: {
        //
    },
    data()
    {
        return {
            token: ''
        }
    },
    mounted() {
        this.token = localStorage.getItem('token')
    },
}
</script>
