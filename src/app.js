$(document).ready(function() {
    // --- SYNTHETIC KEYBOARD CLICK ---
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playKeyClick() {
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(120 + Math.random() * 40, audioCtx.currentTime);
        
        gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.04);
        
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        osc.start();
        osc.stop(audioCtx.currentTime + 0.04);
    }

    // --- PARTICLE EFFECT ---
    const canvas = document.getElementById('particleCanvas');
    const ctx = canvas.getContext('2d');
    let particles = [];

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    $(window).resize(resizeCanvas);

    for (let i = 0; i < 40; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 2 + 1,
            speedY: Math.random() * 0.4 + 0.1,
            opacity: Math.random() * 0.5 + 0.2
        });
    }

    function animateParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(p => {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(165, 180, 252, ${p.opacity})`;
            ctx.fill();
            p.y -= p.speedY;
            if (p.y < 0) p.y = canvas.height;
        });
        requestAnimationFrame(animateParticles);
    }
    animateParticles();

    // --- LOCAL STORAGE (THEME & VOLUME & HIGH SCORE) ---
    const savedTheme = localStorage.getItem('cozyTheme') || '1';
    $('.theme-btn').removeClass('bg-purple-600 text-white shadow-md').addClass('glass-card text-indigo-200');
    $(`button[data-theme="${savedTheme}"]`).removeClass('glass-card text-indigo-200').addClass('bg-purple-600 text-white shadow-md');
    $('.bg-theme').removeClass('active');
    $('#theme' + savedTheme).addClass('active');

    const personalBest = localStorage.getItem('cozyPersonalBest') || 0;
    $('#personalBestDisplay').text(personalBest + ' WPM');

    // --- THEME SWITCHER ---
    $('.theme-btn').click(function() {
        $('.theme-btn').removeClass('bg-purple-600 text-white shadow-md').addClass('glass-card text-indigo-200');
        $(this).removeClass('glass-card text-indigo-200').addClass('bg-purple-600 text-white shadow-md');
        
        const themeId = $(this).data('theme');
        $('.bg-theme').removeClass('active');
        $('#theme' + themeId).addClass('active');
        localStorage.setItem('cozyTheme', themeId);
    });

    // --- MOBILE MENU ---
    $('#mobileMenuBtn').click(function() {
        $('#mobileMenu').slideToggle('fast');
    });
    $('#mobileMenu a').click(function() {
        $('#mobileMenu').slideUp('fast');
    });

    // --- DROPDOWNS & KEYBOARD SHORTCUTS ---
    $('#pomoMenuBtn').click(function(e) {
        e.stopPropagation();
        $('#pomoDropdown').toggleClass('hidden');
        $('#soundDropdown').addClass('hidden');
    });

    $('#soundMenuBtn').click(function(e) {
        e.stopPropagation();
        $('#soundDropdown').toggleClass('hidden');
        $('#pomoDropdown').addClass('hidden');
    });

    $(document).click(function() {
        $('#pomoDropdown').addClass('hidden');
        $('#soundDropdown').addClass('hidden');
    });

    $('#pomoDropdown, #soundDropdown').click(function(e) {
        e.stopPropagation();
    });

    // Global Keyboard Shortcuts (Ctrl + Space for Pomodoro, Esc to close menus)
    $(document).keydown(function(e) {
        if (e.key === 'Escape') {
            $('#pomoDropdown, #soundDropdown, #alarmModal').addClass('hidden');
        }
        if (e.ctrlKey && e.code === 'Space') {
            e.preventDefault();
            $('#pomoMenuBtn').click();
        }
    });

    // --- AUDIO & EQUALIZER SYSTEM ---
    const lofiAudio = document.getElementById('audioLofi');
    const rainAudio = document.getElementById('audioRain');
    const cosmicAudio = document.getElementById('audioCosmic');
    
    const savedVolume = localStorage.getItem('cozyVolume') || 0.5;
    $('#volumeSlider').val(savedVolume);
    lofiAudio.volume = savedVolume;
    rainAudio.volume = savedVolume;
    cosmicAudio.volume = savedVolume;

    lofiAudio.play().catch(() => {});

    function updateEqualizerState(isMuted) {
        if (isMuted) {
            $('#equalizer span').removeClass('eq-bar-1 eq-bar-2 eq-bar-3').css('height', '4px').removeClass('bg-emerald-400').addClass('bg-rose-500');
        } else {
            $('#equalizer span').addClass('eq-bar-1 eq-bar-2 eq-bar-3').removeClass('bg-rose-500').addClass('bg-emerald-400');
        }
    }

    $('#soundDropdown button[data-sound]').click(function() {
        lofiAudio.pause();
        rainAudio.pause();
        cosmicAudio.pause();

        const soundType = $(this).data('sound');
        
        if (soundType === 'lofi') {
            lofiAudio.play().catch(() => {});
            $('#currentSoundLabel').text('Audio: Lofi');
            updateEqualizerState(false);
        } else if (soundType === 'rain') {
            rainAudio.play().catch(() => {});
            $('#currentSoundLabel').text('Audio: Rain');
            updateEqualizerState(false);
        } else if (soundType === 'cosmic') {
            cosmicAudio.play().catch(() => {});
            $('#currentSoundLabel').text('Audio: Cosmic');
            updateEqualizerState(false);
        } else if (soundType === 'off') {
            $('#currentSoundLabel').text('Audio: Muted');
            updateEqualizerState(true);
        }
    });

    $('#volumeSlider').on('input', function() {
        const val = $(this).val();
        lofiAudio.volume = val;
        rainAudio.volume = val;
        cosmicAudio.volume = val;
        localStorage.setItem('cozyVolume', val);
    });

    // --- POMODORO TIMER ---
    let pomoTime = 25 * 60;
    let pomoTimerId = null;
    let pomoRunning = false;
    let pomoMode = 'work';
    const alarmAudio = document.getElementById('alarmSound');

    function formatTime(seconds) {
        let m = Math.floor(seconds / 60);
        let s = seconds % 60;
        return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
    }

    function updatePomoDisplays() {
        let timeStr = formatTime(pomoTime);
        $('#pomoDisplay').text(timeStr);
        $('#navPomoTime').text(timeStr);
    }

    $('#pomoWorkBtn').click(function() {
        clearInterval(pomoTimerId);
        pomoRunning = false;
        pomoMode = 'work';
        pomoTime = 25 * 60;
        updatePomoDisplays();
        $('#pomoToggleBtn').text('Start');
        $(this).addClass('bg-purple-600 text-white shadow-md').removeClass('glass-card text-indigo-200');
        $('#pomoRestBtn').removeClass('bg-purple-600 text-white shadow-md').addClass('glass-card text-indigo-200');
    });

    $('#pomoRestBtn').click(function() {
        clearInterval(pomoTimerId);
        pomoRunning = false;
        pomoMode = 'rest';
        pomoTime = 5 * 60;
        updatePomoDisplays();
        $('#pomoToggleBtn').text('Start');
        $(this).addClass('bg-purple-600 text-white shadow-md').removeClass('glass-card text-indigo-200');
        $('#pomoWorkBtn').removeClass('bg-purple-600 text-white shadow-md').addClass('glass-card text-indigo-200');
    });

    $('#pomoToggleBtn').click(function() {
        if (!pomoRunning) {
            pomoRunning = true;
            $('#pomoToggleBtn').text('Pause');
            pomoTimerId = setInterval(() => {
                if (pomoTime > 0) {
                    pomoTime--;
                    updatePomoDisplays();
                } else {
                    clearInterval(pomoTimerId);
                    pomoRunning = false;
                    $('#pomoToggleBtn').text('Start');
                    
                    alarmAudio.currentTime = 0;
                    alarmAudio.play().catch(() => {});
                    $('#alarmModal').removeClass('hidden');
                }
            }, 1000);
        } else {
            clearInterval(pomoTimerId);
            pomoRunning = false;
            $('#pomoToggleBtn').text('Resume');
        }
    });

    $('#dismissAlarmBtn').click(function() {
        alarmAudio.pause();
        alarmAudio.currentTime = 0;
        $('#alarmModal').addClass('hidden');
    });

    $('#pomoResetBtn').click(function() {
        clearInterval(pomoTimerId);
        pomoRunning = false;
        pomoTime = pomoMode === 'work' ? 25 * 60 : 5 * 60;
        updatePomoDisplays();
        $('#pomoToggleBtn').text('Start');
    });

    // --- BREATHING WIDGET ---
    let breathState = 'stopped';
    let breathTimeouts = [];

    function clearBreathTimeouts() {
        breathTimeouts.forEach(t => clearTimeout(t));
        breathTimeouts = [];
    }

    function runBreathingCycle() {
        if (breathState !== 'running') return;
        const $circle = $('#breathCircle');

        $circle.text('Inhale...').css('transform', 'scale(1.25)');
        breathTimeouts.push(setTimeout(() => {
            if (breathState !== 'running') return;
            
            $circle.text('Sip More...').css('transform', 'scale(1.4)');
            breathTimeouts.push(setTimeout(() => {
                if (breathState !== 'running') return;
                
                $circle.text('Exhale...').css('transform', 'scale(0.85)');
                breathTimeouts.push(setTimeout(() => {
                    if (breathState !== 'running') return;
                    runBreathingCycle();
                }, 4000));
            }, 1200));
        }, 2500));
    }

    $('#breathBtn').click(function() {
        if (breathState === 'stopped' || breathState === 'paused') {
            breathState = 'running';
            $(this).text('Pause');
            $('#restartBreathBtn').removeClass('hidden');
            runBreathingCycle();
        } else if (breathState === 'running') {
            breathState = 'paused';
            clearBreathTimeouts();
            $(this).text('Resume');
            $('#breathCircle').text('Paused');
        }
    });

    $('#restartBreathBtn').click(function() {
        breathState = 'stopped';
        clearBreathTimeouts();
        $('#breathCircle').css('transform', 'scale(1)').text('Ready');
        $('#breathBtn').text('Start Breathing');
        $(this).addClass('hidden');
    });

    // --- ZEN TYPING GAME WITH HIGH SCORE TRACKER ---
    let snippets = [
        "const findPeace = () => {\n    let mind = 'clear';\n    return true;\n};",
        "function compileCode() {\n    let bugs = 0;\n    console.log('Ready!');\n}",
        "const student = {\n    name: 'IT Scholar',\n    status: 'Focused'\n};"
    ];
    
    let currentSnippetIndex = 0;
    let currentIndex = 0;
    let targetText = snippets[currentSnippetIndex];
    
    let typeStartTime = null;
    let totalKeysPressed = 0;
    let mistakeKeys = 0;

    $('#loadCustomSnippetBtn').click(function() {
        const customVal = $('#customSnippetInput').val().trim();
        if (customVal) {
            snippets.push(customVal);
            currentSnippetIndex = snippets.length - 1;
            targetText = snippets[currentSnippetIndex];
            renderSnippet();
            $('#customSnippetInput').val('');
            $('#typingCard').focus();
        }
    });

    function skipWhitespaceAndNewlines() {
        while (currentIndex < targetText.length && (targetText[currentIndex] === ' ' || targetText[currentIndex] === '\n')) {
            let targetDomSpan = null;
            let spanCounter = 0;
            $('#codeDisplay').contents().each(function() {
                if (this.nodeName === 'SPAN') {
                    if (spanCounter === currentIndex) {
                        targetDomSpan = this;
                        return false;
                    }
                    spanCounter++;
                }
            });

            if (targetDomSpan) {
                $(targetDomSpan).removeClass('char-cursor char-pending').addClass('char-typed');
            }
            currentIndex++;
        }
    }

    function updateHint() {
        if (currentIndex < targetText.length) {
            const nextChar = targetText[currentIndex];
            if (nextChar === '\n') {
                $('#nextKeyHint').text('⌨️ Press [Enter]');
            } else if (nextChar === ' ') {
                $('#nextKeyHint').text('⌨️ Press [Space]');
            } else {
                $('#nextKeyHint').text('');
            }
        } else {
            $('#nextKeyHint').text('');
        }
    }

    function renderSnippet() {
        const $display = $('#codeDisplay');
        $display.empty();
        for (let i = 0; i < targetText.length; i++) {
            const char = targetText[i];
            let spanHtml;
            if (char === '\n') {
                spanHtml = '<br>';
            } else {
                let cssClass = (i === 0) ? 'char-cursor' : 'char-pending';
                spanHtml = $('<span>').text(char).addClass(cssClass)[0].outerHTML;
            }
            $display.append(spanHtml);
        }
        
        currentIndex = 0;
        typeStartTime = null;
        totalKeysPressed = 0;
        mistakeKeys = 0;
        
        skipWhitespaceAndNewlines();
        
        let firstSpan = null;
        let spanCounter = 0;
        $('#codeDisplay').contents().each(function() {
            if (this.nodeName === 'SPAN') {
                if (spanCounter === currentIndex) {
                    firstSpan = this;
                    return false;
                }
                spanCounter++;
            }
        });
        if (firstSpan) {
            $(firstSpan).removeClass('char-pending').addClass('char-cursor');
        }

        updateHint();
        $('#completionOverlay').addClass('hidden');
    }

    renderSnippet();

    $('#typingCard').on('focus', function() {
        $(this).addClass('border-purple-500 shadow-purple-900/40 ring-2 ring-purple-500/30');
        $('#cardStatusText').text('🟢 Ready to Type (Active)').removeClass('text-indigo-300/60').addClass('text-emerald-400 font-bold');
    }).on('blur', function() {
        $(this).removeClass('border-purple-500 shadow-purple-900/40 ring-2 ring-purple-500/30');
        $('#cardStatusText').text('💡 Click card to activate typing...').addClass('text-indigo-300/60').removeClass('text-emerald-400 font-bold');
        $('#nextKeyHint').text('');
    });

    $('#typingCard').on('keydown', function(e) {
        if (e.key.length > 1 && e.key !== 'Enter') return;

        if (!typeStartTime) typeStartTime = Date.now();
        totalKeysPressed++;

        let expectedChar = targetText[currentIndex];
        let typedChar = e.key;

        if (e.key === 'Enter') {
            typedChar = '\n';
        }

        if (typedChar === expectedChar) {
            playKeyClick();

            let spanCounter = 0;
            let targetDomSpan = null;
            $('#codeDisplay').contents().each(function() {
                if (this.nodeName === 'SPAN') {
                    if (spanCounter === currentIndex) {
                        targetDomSpan = this;
                        return false;
                    }
                    spanCounter++;
                }
            });

            if (targetDomSpan) {
                $(targetDomSpan).removeClass('char-cursor').addClass('char-typed');
            }
            
            currentIndex++;
            skipWhitespaceAndNewlines();

            let nextDomSpan = null;
            spanCounter = 0;
            $('#codeDisplay').contents().each(function() {
                if (this.nodeName === 'SPAN') {
                    if (spanCounter === currentIndex) {
                        nextDomSpan = this;
                        return false;
                    }
                    spanCounter++;
                }
            });

            if (nextDomSpan) {
                $(nextDomSpan).removeClass('char-pending').addClass('char-cursor');
            }

            updateHint();

            if (currentIndex >= targetText.length) {
                $('#nextKeyHint').text('');
                
                const endTime = Date.now();
                const minutes = (endTime - typeStartTime) / 60000;
                let wpm = Math.round((targetText.length / 5) / minutes);
                if (!isFinite(wpm) || wpm > 300) wpm = 0;
                
                let accuracy = Math.round(((totalKeysPressed - mistakeKeys) / totalKeysPressed) * 100);
                if (accuracy < 0) accuracy = 0;

                // Check and save Personal Best
                const currentBest = parseInt(localStorage.getItem('cozyPersonalBest') || 0);
                if (wpm > currentBest) {
                    localStorage.setItem('cozyPersonalBest', wpm);
                    $('#personalBestDisplay').text(wpm + ' WPM');
                }

                $('#wpmDisplay').text(wpm);
                $('#accuracyDisplay').text(accuracy);
                
                $('#completionOverlay').removeClass('hidden');
            }
        } else {
            mistakeKeys++;
        }
        
        if(e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
        }
    });

    $('#resetTypeBtn, #nextSnippetBtn').click(function(e) {
        e.stopPropagation();
        currentSnippetIndex = (currentSnippetIndex + 1) % snippets.length;
        targetText = snippets[currentSnippetIndex];
        renderSnippet();
        $('#typingCard').focus();
    });

    // --- CONTACT FORM LOGIC ---
    $('#contactForm').submit(function(e) {
        e.preventDefault();
        const name = $('#senderName').val();
        if(name) {
            $(this.elements).prop('disabled', true);
            $('#formSuccess').removeClass('hidden').hide().fadeIn(300);
        }
    });
});