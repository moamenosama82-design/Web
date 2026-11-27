// js/game.js
// Arabic Competence Exam - Client-side exam management

let questions = [];
let currentIndex = 0;
let answers = {};
let examSubmitted = false;
let finalScore = 0;
let totalQuestions = 0;

// Load questions from server
async function loadQuestions() {
    const container = document.getElementById('question-container');
    if (container) {
        container.innerHTML = '<div style="text-align:center; padding:40px;">Loading questions...</div>';
    }
    
    try {
        const url = `api/get_questions.php?level=${GAME_LEVEL}&limit=${TOTAL_QUESTIONS}`;
        console.log('Fetching from:', url);
        
        const res = await fetch(url);
        const text = await res.text();
        console.log('Server response length:', text.length);
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e);
            if (container) {
                container.innerHTML = '<div style="color:red;">Error loading questions. Please check database connection.</div>';
            }
            return;
        }
        
        if (!Array.isArray(data) || data.length === 0) {
            if (container) {
                container.innerHTML = '<div style="color:orange;">No questions found for this level. Please add questions to the database.</div>';
            }
            return;
        }
        
        questions = data;
        totalQuestions = questions.length;
        
        // Update UI
        document.getElementById('nextBtn').disabled = false;
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('prevBtn').disabled = true;
        
        showQuestion();
        updateProgressUI();
        
    } catch (error) {
        console.error('Fetch error:', error);
        if (container) {
            container.innerHTML = '<div style="color:red;">Server connection error. Please make sure the server is running.</div>';
        }
    }
}

// Display current question
function showQuestion() {
    if (!questions.length || currentIndex >= questions.length) return;
    
    const q = questions[currentIndex];
    const container = document.getElementById('question-container');
    
    let html = `
        <div style="margin-bottom:20px;">
            <div style="font-size:14px; color:#93c5fd; margin-bottom:10px;">
                Question ${currentIndex + 1} of ${questions.length}
            </div>
            <div id="question-text" style="font-size:18px; line-height:1.6;">
                ${escapeHtml(q.question_text)}
            </div>
        </div>
    `;
    
    q.options.forEach((opt, idx) => {
        const checked = answers[q.id] === opt ? 'checked' : '';
        const letter = String.fromCharCode(65 + idx); // A, B, C, D
        html += `
            <label class="option" style="display:block; margin:8px 0; padding:10px; border-radius:8px; background:rgba(15,23,42,0.85); cursor:pointer;">
                <input type="radio" name="answer" value="${escapeHtml(opt)}" ${checked} style="margin-right:10px;">
                <span style="font-weight:bold;">${letter}.</span> ${escapeHtml(opt)}
            </label>
        `;
    });
    
    container.innerHTML = html;
    
    // Add event listeners to options
    document.querySelectorAll('input[name="answer"]').forEach(radio => {
        radio.addEventListener('change', () => saveCurrentAnswer());
    });
    
    // Update navigation buttons
    document.getElementById('prevBtn').disabled = (currentIndex === 0);
    document.getElementById('nextBtn').disabled = (examSubmitted);
    
    updateProgressUI();
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Save current answer
function saveCurrentAnswer() {
    if (examSubmitted) return;
    
    const q = questions[currentIndex];
    const selected = document.querySelector('input[name="answer"]:checked');
    if (selected) {
        answers[q.id] = selected.value;
        console.log(`Saved answer for Q${q.id}: ${selected.value}`);
    }
}

// Calculate score and display result
function calculateAndShowScore() {
    if (examSubmitted) return;
    
    // Save current answer before calculation
    saveCurrentAnswer();
    
    // Calculate correct answers
    let correctCount = 0;
    questions.forEach(q => {
        if (answers[q.id] === q.correct) {
            correctCount++;
        }
    });
    
    finalScore = correctCount;
    totalQuestions = questions.length;
    const percent = Math.round((finalScore / totalQuestions) * 100);
    
    // Star rating
    let stars = '';
    let feedback = '';
    
    if (percent >= 80) {
        stars = '★★★★★';
        feedback = 'Excellent! High language proficiency. You have mastered Arabic very well.';
    } else if (percent >= 60) {
        stars = '★★★★☆';
        feedback = 'Very good! You have a good understanding of Arabic. Keep practicing to improve further.';
    } else if (percent >= 40) {
        stars = '★★★☆☆';
        feedback = 'Good! You need more practice to improve your level.';
    } else {
        stars = '★★☆☆☆';
        feedback = 'Needs improvement! Keep studying and try again.';
    }
    
    // Display result
    const resultContainer = document.getElementById('resultContainer');
    const resultStars = document.getElementById('resultStars');
    const resultScore = document.getElementById('resultScore');
    const resultFeedback = document.getElementById('resultFeedback');
    const saveStatus = document.getElementById('saveStatus');
    
    resultStars.innerHTML = stars;
    resultScore.innerHTML = `${finalScore} / ${totalQuestions} (${percent}%)`;
    resultFeedback.innerHTML = feedback;
    saveStatus.innerHTML = 'Saving your result...';
    saveStatus.className = 'saving';
    
    resultContainer.style.display = 'block';
    
    examSubmitted = true;
    
    // Disable navigation and answer buttons
    document.getElementById('nextBtn').disabled = true;
    document.getElementById('prevBtn').disabled = true;
    document.getElementById('submitBtn').disabled = true;
    document.querySelectorAll('input[name="answer"]').forEach(radio => {
        radio.disabled = true;
    });
    
    // Save result to database
    saveResult(GAME_LEVEL, finalScore, totalQuestions);
}

// Save result to database
async function saveResult(level, score, total) {
    const saveStatus = document.getElementById('saveStatus');
    
    try {
        const params = new URLSearchParams();
        params.append('level', level);
        params.append('score', score);
        params.append('total', total);
        
        const res = await fetch('api/save_result.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        });
        
        const text = await res.text();
        console.log('save_result response:', text);
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            saveStatus.innerHTML = 'Score calculated but save failed.';
            saveStatus.className = '';
            return;
        }
        
        if (data.status === 'ok') {
            saveStatus.innerHTML = 'Result saved successfully!';
            saveStatus.className = '';
        } else {
            saveStatus.innerHTML = 'Could not save result. Please try again.';
            saveStatus.className = '';
        }
    } catch (err) {
        console.error(err);
        saveStatus.innerHTML = 'Error occurred while saving result.';
        saveStatus.className = '';
    }
}

// Update progress bar
function updateProgressUI() {
    const fill = document.getElementById('progressFill');
    const text = document.getElementById('progressText');
    
    if (!fill || !text) return;
    
    if (!questions.length) {
        fill.style.width = '0%';
        text.innerHTML = '0 of 0';
        return;
    }
    
    const percent = Math.round(((currentIndex + 1) / questions.length) * 100);
    fill.style.width = percent + '%';
    text.innerHTML = `Question ${currentIndex + 1} of ${questions.length} (${percent}%)`;
}

// Reset exam
function resetExam() {
    if (confirm('Are you sure you want to reset the exam? All your answers will be cleared.')) {
        answers = {};
        currentIndex = 0;
        examSubmitted = false;
        
        // Hide result
        document.getElementById('resultContainer').style.display = 'none';
        
        // Reload questions
        loadQuestions();
    }
}

// Navigate to next question
function nextQuestion() {
    saveCurrentAnswer();
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        showQuestion();
    }
}

// Navigate to previous question
function previousQuestion() {
    saveCurrentAnswer();
    if (currentIndex > 0) {
        currentIndex--;
        showQuestion();
    }
}

// Initialize buttons when page loads
window.onload = () => {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');
    
    if (nextBtn) nextBtn.onclick = nextQuestion;
    if (prevBtn) prevBtn.onclick = previousQuestion;
    if (submitBtn) submitBtn.onclick = calculateAndShowScore;
    if (resetBtn) resetBtn.onclick = resetExam;
    
    loadQuestions();
};