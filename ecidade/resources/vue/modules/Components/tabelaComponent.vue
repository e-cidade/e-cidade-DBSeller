<template>
<div>
    <table>
        <thead>
            <th v-for="header of headers">
                {{header}}
            </th>
        </thead>
        <tbody>
            <td></td>
        </tbody>
    </table>
</div>
</template>

<script>
export default {
    props:['headers','data']
}
</script>

<style scoped>

</style>
