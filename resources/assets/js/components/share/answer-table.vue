<template>

<div>
    <div v-for="(userAnswer, index) in userAnswers">
        <h3 class="subtitle">第{{ index + 1 }}文</h3>
        <table>
            <tr>
                <th></th>
                <th>正誤</th>
                <th>最後の単語</th>
            </tr>
            <tr>
                <td>正解</td>
                <td>{{ correctAnswers[index].correctness ? "正しい" : "誤り" }}</td>
                <td>{{ correctAnswers[index].lastWord }}</td>
            </tr>
            <tr>
                <td>あなたの回答</td>
                <td>{{ userAnswer.judgement ? "正しい" : "誤り" }}</td>
                <td>{{ userAnswer.word }}</td>
            </tr>    
        </table>
    </div>
</div>

</template>


<script>
export default {
    data () {
        return {
            
        }
    },
    props: {
        userAnswers: Array,
        correctAnswers: Array
    },
    computed: {
        
    }
}
</script>

<style scoped>
th, td {
    padding: 4px;
}
table, th, td {
    border: 1px solid black;
}
</style>