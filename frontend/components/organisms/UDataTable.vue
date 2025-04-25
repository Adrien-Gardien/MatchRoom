<script setup lang="ts">
import type { Column } from './UTable.vue';

interface DataTableProps {
    endpoint: string
    columns: Column[],
}

const data = ref();

const { $api } = useNuxtApp();
const props = withDefaults(defineProps<DataTableProps>(), {

})

const fetchData = async () => {
    const {data: fetchedData} = await useAuthFetch($api(props.endpoint));

    if (fetchedData) {
        data.value = fetchedData;
    }
}

watchEffect(fetchData);

</script>

<template>
    <UTable
        :columns="columns"
        :data="data"
    />
</template>