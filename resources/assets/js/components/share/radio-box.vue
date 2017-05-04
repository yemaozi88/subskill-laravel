<template>

<div class="form-group form-inline">
    <label :id="labelId" class="control-label">
        <slot></slot>
    </label>
    <div v-for="(text, index) in items" class="radio">
        <label>
            <input 
                type="radio" :name="radioName"
                :value="index + 1" :checked="value == index + 1"
                v-on:click="onValueChanged($event.target.value)">
            {{ text }}
        </label>
    </div>
</div>

</template>

<script>
require("../../helpers");

export default {
    data () {
        return {
        }
    },
    props: {
        label: {
            type: String,
            default: function () {
                return "";
            }
        },
        items: {
            type: Array,
            required: true
        },
        idPrefix: {
            type: String,
            default: function () {
                return Helpers.getUniqueStr();
            }
        },
        value: {
            type: Number
        }
    },
    computed: {
        labelId() {
            return this.idPrefix + '_label';
        },
        radioName() {
            return this.idPrefix + '_radio';
        }
    },
    methods: {
        onValueChanged (value) {
            this.$emit("input", parseInt(value));
        }
    }
}
</script>