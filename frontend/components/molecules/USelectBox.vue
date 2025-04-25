<script setup lang="ts">
import ChrevronDownIcon from '~/components/atoms/icons/ChrevronDownIcon.vue';
import { ref, computed, watch, onMounted } from 'vue';

interface SelectOption {
    value: string | number;
    label: string;
}

interface SelectBoxProps {
    label: string;
    options: SelectOption[];
    modelValue?: string | number;
    placeholder?: string;
    position?: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
}

const props = withDefaults(defineProps<SelectBoxProps>(), {
    position: 'bottom-left',
    placeholder: 'Sélectionner une option',
    modelValue: ''
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const selectBoxPosition = ref<string>(props.position);
const selectedOption = ref<SelectOption | null>(null);

const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    
    return props.options.filter(option => 
        option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const displayValue = computed(() => {
    if (selectedOption.value) return selectedOption.value.label;
    return props.placeholder;
});

function toggleDropdown() {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        searchQuery.value = '';
    }
}

function selectOption(option: SelectOption) {
    selectedOption.value = option;
    emit('update:modelValue', option.value);
    isOpen.value = false;
}

watch(() => props.modelValue, (newVal) => {
    if (newVal !== undefined && newVal !== null) {
        const option = props.options.find(opt => opt.value === newVal);
        if (option) {
            selectedOption.value = option;
        }
    } else {
        selectedOption.value = null;
    }
}, { immediate: true });

onMounted(() => {
    document.addEventListener('click', (e: MouseEvent) => {
        const target = e.target as HTMLElement;
        if (target && !target.closest('.selectbox-container')) {
            isOpen.value = false;
        }
    });
});
</script>

<template>
    <div class="selectbox-container relative inline-block w-full">
        <div 
            class="flex items-center justify-between px-3 py-2 border border-secondary-alt bg-primary rounded-lg cursor-pointer hover:bg-primary-hover transition-colors"
            @click="toggleDropdown"
        >
            <div class="text-sm font-semibold truncate">
                {{ displayValue }}
            </div>
            <ChrevronDownIcon class="ml-2 size-4 text-fg-quaternary" />
        </div>

        <Transition :name="`dropdown-${selectBoxPosition}`">
            <div
                v-if="isOpen"
                class="absolute w-full rounded-lg shadow-lg bg-primary border border-secondary-alt z-10"
                :class="{
                    'top-auto bottom-full mb-2': selectBoxPosition.startsWith('top'),
                    'top-full mt-2': selectBoxPosition.startsWith('bottom'),
                    'left-0': selectBoxPosition.endsWith('left'),
                    'right-0': selectBoxPosition.endsWith('right'),
                }"
            >
                <div class="p-2">
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="w-full px-3 py-2 text-sm border border-secondary-alt rounded-md mb-2 bg-primary focus:outline-none focus:ring-1 focus:ring-secondary"
                        placeholder="Rechercher..."
                        @click.stop
                    />
                    
                    <div class="max-h-60 overflow-y-auto">
                        <template v-if="filteredOptions.length > 0">
                            <div 
                                v-for="option in filteredOptions" 
                                :key="option.value" 
                                class="px-3 py-2 text-sm flex items-center hover:bg-primary-hover rounded-md cursor-pointer transition-colors"
                                :class="{'font-semibold': selectedOption?.value === option.value}"
                                @click="selectOption(option)"
                            >
                                {{ option.label }}
                            </div>
                        </template>
                        <div v-else class="px-3 py-2 text-sm text-fg-quaternary">
                            Aucun résultat
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.dropdown-bottom-right-enter-active,
.dropdown-bottom-right-leave-active,
.dropdown-bottom-left-enter-active,
.dropdown-bottom-left-leave-active {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}

.dropdown-bottom-right-enter-from,
.dropdown-bottom-right-leave-to,
.dropdown-bottom-left-enter-from,
.dropdown-bottom-left-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.dropdown-top-right-enter-active,
.dropdown-top-right-leave-active,
.dropdown-top-left-enter-active,
.dropdown-top-left-leave-active {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}

.dropdown-top-right-enter-from,
.dropdown-top-right-leave-to,
.dropdown-top-left-enter-from,
.dropdown-top-left-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>