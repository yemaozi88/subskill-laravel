<template>

<div>
    <p>音声を聞いて、<strong>{{ orderText }}</strong>数字を入力してください。</p>
    <button class="btn btn-default" :disabled="isShowQuestionBtnDisabled" 
            v-on:click="onShowQuestionBtnClicked">問題を再生</button>
    <audio-player :urls="question.urls" v-on:loaded="onAudioLoaded" v-on:ended="onAudioEnded"
            ref="player"></audio-player>
</div>

</template>



<script>
import AudioPlayer from "../share/audio-player"

export default {
    components: {
        "audio-player": AudioPlayer
    },
    data () {
        return {
            state: 1,
            audioLoaded: false
        }
    },
    props: {
        /**
         * Example:
         * {num: 2, order: "F", sequence: [1, 2], urls: ["1.wav", "2.wav"]}
         */
        question: Object
    },
    computed: {
        isShowQuestionBtnDisabled () {
            return this.state != 1 || !this.audioLoaded;
        },
        orderText () {
            return this.question.order == "F" ? "聞いた順番通りに" : "聞いた順番と逆順に";
        }
    },
    methods: {
        onShowQuestionBtnClicked () {
            this.state = 2;
            // TODO(sonicmisora): find a way to avoid use $refs.
            this.$refs.player.play();
        },
        onAudioLoaded () {
            this.audioLoaded = true;
        },
        onAudioEnded () {
            this.$emit("question-displayed");
        }
    }
}

</script>
