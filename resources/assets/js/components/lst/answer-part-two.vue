<template>

<div>
    <div v-for="(quest, index) in questions" class="form-group form-inline" :key="quest.id">
        <label class="control-label">
            ({{ index + 1 }}){{ index + 1 }}文目最後の単語:
        </label>
        {{ quest.lastWord[0] }}
        <input type="text" class=""
            v-on:input="onAnswerChanged(index, $event.target.value)">
    </div>
</div>

</template>


<script>

export default {
    data () {
        return {
            answers: []
        }
    },
    props: {
        questions: Array
    },
    computed: {
        
    },
    methods: {
        onAnswerChanged(index, value) {
            while (this.questions.length > this.answers.length) {
                this.answers.push(null);
            }
            while (this.questions.length < this.answers.length) {
                this.answers.pop();
            }
            this.answers.splice(index, 1, this.questions[index].lastWord[0] + value);
            
            this.$emit("changed", this.answers);
        }
    },
    mounted() {
        for (var i = 0; i < this.questions.length; i++) {
            this.onAnswerChanged(i, "");
        }
    }
}
</script>