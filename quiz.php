<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$apiKey = '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';

function callAIQuiz($matiere, $lesson, $numQuestions, $apiKey) {
    $messages = [
        ["role" => "system", "content" => "You are an assistant generating multiple choice quizzes in JSON format."],
        ["role" => "user", "content" => "Generate a $numQuestions-question multiple choice quiz on the topic: $matiere - $lesson. Use this JSON format:
        {
            \"quiz\": [
                {
                    \"question\": \"...\",
                    \"options\": [\"A\", \"B\", \"C\", \"D\"],
                    \"answer\": \"B\"
                }
            ]
        }"]
    ];

    $data = [
        "model" => "openai/gpt-3.5-turbo",
        "messages" => $messages,
        "temperature" => 0.7,
        "max_tokens" => 1500
    ];

    $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? null;
}

$quiz = null;
$score = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['matiere'], $_POST['lesson'], $_POST['numQuestions'])) {
        $matiere = htmlspecialchars($_POST['matiere']);
        $lesson = htmlspecialchars($_POST['lesson']);
        $numQuestions = intval($_POST['numQuestions']);
        if ($numQuestions < 1) $numQuestions = 1;
        if ($numQuestions > 10) $numQuestions = 10;

        $jsonQuiz = callAIQuiz($matiere, $lesson, $numQuestions, $apiKey);

        if ($jsonQuiz) {
            $decoded = json_decode($jsonQuiz, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['quiz'])) {
                $_SESSION['quiz'] = $decoded['quiz'];
                header("Location: quiz.php");
                exit();
            } else {
                $error = "Erreur de format JSON de l'IA.";
            }
        } else {
            $error = "Pas de réponse de l'IA.";
        }
    } elseif (isset($_POST['answers'], $_SESSION['quiz'])) {
        $answers = $_POST['answers'];
        $quiz = $_SESSION['quiz'];
        $score = 0;

        foreach ($quiz as $index => $q) {
            if (isset($answers[$index]) && strtoupper($answers[$index]) === strtoupper($q['answer'])) {
                $score++;
            }
        }

        unset($_SESSION['quiz']);
    }
}

if (isset($_SESSION['quiz'])) {
    $quiz = $_SESSION['quiz'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz IA Dynamique</title>
    <link rel="stylesheet" href="quiz_style.css">
</head>
<body>
<div class="dashboard-layout">
    <div class="quiz-card">
        <header>
            <h1>AI-Powered Quiz</h1>
            <p>Create a quiz by selecting your subject, topic, and number of questions.</p>
        </header>

        <main>
            <?php if (!empty($error)) : ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <?php if (!$quiz && $score === null): ?>
                <form method="post" class="quiz-form">
                    <label for="matiere">Subject</label>
                    <input type="text" name="matiere" id="matiere" required>

                    <label for="lesson">Topic</label>
                    <input type="text" name="lesson" id="lesson"  required>

                    <label for="numQuestions">Number of Questions (1-10)</label>
                    <input type="number" name="numQuestions" id="numQuestions" min="1" max="10" value="5" required>

                    <button type="submit">Generate Quiz</button>
                </form>
            <?php elseif ($quiz && $score === null): ?>
                <form method="post" class="quiz-questions">
                    <?php foreach ($quiz as $i => $q): ?>
                        <div class="question-block">
                            <p><strong><?= ($i+1) . ". " . htmlspecialchars($q['question']) ?></strong></p>
                            <?php foreach ($q['options'] as $opt): ?>
                                <label>
                                    <input type="radio" name="answers[<?= $i ?>]" value="<?= htmlspecialchars($opt) ?>" required>
                                    <?= htmlspecialchars($opt) ?>
                                </label><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit">Submit Answers</button>
                </form>
            <?php elseif ($score !== null): ?>
                <div class="result">
                    <h2>Your Score</h2>
                    <p>You answered <strong><?= $score ?>/<?= count($quiz) ?></strong> correctly.</p>
                    <a href="quiz.php" class="btn-primary">Try Another Quiz</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
