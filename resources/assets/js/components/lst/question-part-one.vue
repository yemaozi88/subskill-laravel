<template>

<div>
    <h3>第{{ index + 1 }}文</h3>
    <button class="btn btn-default" :disabled="isShowQuestionBtnDisabled" v-on:click="onShowQuestionBtnClicked">
        問題を再生
    </button>
    <div v-if="ifShowCheckBox">
        <radio-box :items="['正しい', '誤り']" v-model="currAnswer">
            文の正誤：
        </radio-box>
        <button 
            v-on:click="onSubmitAnswerBtnClicked" class="btn btn-default"
            :disabled="isSubmitAnswerBtnDisabled">次へ</button>
    </div>
    <audio-player :urls="currWavUrl" v-on:loaded="onAudioLoaded" v-on:ended="onAudioEnded" ref="player">
    </audio-player>
</div>

</template>

<script>
import AudioPlayer from "../share/audio-player"
import RadioBox from "../share/radio-box"

export default {
    components: {
        "audio-player": AudioPlayer,
        "radio-box": RadioBox
    },
    data () {
        return {
            state: 1,
            audioLoaded: false,
            currAnswer: 0,
            answers: [],
            index: 0
        }
    },
    props: {
        wavUrls: Array,
    },
    computed: {
        isShowQuestionBtnDisabled () {
            return this.state != 1 || !this.audioLoaded;
        },
        isSubmitAnswerBtnDisabled () {
            return this.currAnswer == 0;
        },
        currWavUrl() {
            return [this.wavUrls[this.index]];
        },
        ifShowCheckBox() {
            return this.state == 3;
        }
    },
    methods: {
        onShowQuestionBtnClicked () {
            if (this.state == 1) {
                this.state = 2;
                // TODO(sonicmisora): find a way to avoid use $refs.
                this.$refs.player.play();
            }
        },
        onSubmitAnswerBtnClicked() {
            // Assume this.state == 3
            this.answers.push(this.currAnswer);
            if (this.index == this.wavUrls.length - 1) {
                this.$emit("ended", this.answers);
                this.state = 1;
                this.index = 0;
                this.answers = [];
                return;
            }
            this.state = 1;
            this.index++;
            this.currAnswer = 0;
            this.audioLoaded = false;
        },
        onAudioLoaded () {
            this.audioLoaded = true;
        },
        onAudioEnded () {
            //this.$emit("ended");
            this.state = 3;
        },
    },
    watch: {
        currWavUrl() {
            this.$refs.player.reload();
        }
    }
}
</script>

<style scoped>

</style>