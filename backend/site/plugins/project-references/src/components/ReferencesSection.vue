<template>
	<k-section :label="headline || label || $t('project.references.defaultLabel')">
		<k-text v-if="isLoading" theme="help">{{ $t("project.references.loading") }}</k-text>
		<k-text v-else-if="items.length === 0" theme="help">
			{{ $t("project.references.noReferences") }}
		</k-text>
		<k-items v-else :items="items"></k-items>
	</k-section>
</template>

<script setup>
import { ref, onMounted, useSection } from "kirbyuse";
import { section } from "kirbyuse/props";

const props = defineProps({
	headline: String,
	label: String,
	...section
});

const { load: loadSection } = useSection();
const items = ref([]);
const isLoading = ref(true);

const load = async () => {
	isLoading.value = true;
	try {
		const response = await loadSection({ parent: props.parent, name: props.name });
		items.value = response.references || [];
	} catch (e) {
		console.log(e);
		items.value = [];
	}
	isLoading.value = false;
};

onMounted(() => {
	load();
});
</script>
