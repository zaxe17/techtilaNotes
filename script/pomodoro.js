let worktitle = document.getElementById('work');
let breaktitle = document.getElementById('break');

/* MINUTES AND SECOND OF WORK AND BREAK */
let workTime = 25;
let breakTime = 5;
let seconds = "00";
let workMinutes = workTime - 1;
let breakMinutes = breakTime - 1;

/* TIMER PAUSE */
let timer;
let isPaused = false;
let breakcount = 0;

/* TIMER AUDIO FOR WORK AND BREAK */
const audio = new Audio("../sound/tiktilaok.mp3");
const audio2 = new Audio("../sound/alarm.mp3");

/* DISPLAY MINUTES AND SECONDS */
window.onload = () => {
    document.getElementById('minutes').innerHTML = workTime;
    document.getElementById('seconds').innerHTML = seconds;

    worktitle.classList.add('active');
}

/* START BUTTON */
function start() {
    if(isPaused) {
        isPaused = false;
        document.getElementById('start').style.display = 'none';
        document.getElementById('pause').style.display = 'block';
        timer = setInterval(timerFunction, 1000);
        
        return;
    }

    /* DISPLAY ICON PAUSE AND RESET BUTTON */
    document.getElementById('start').style.display = 'none';
    document.getElementById('reset').style.display = 'block';
    document.getElementById('pause').style.display = 'block';

    seconds = 59;
    workMinutes = workTime - 1;
    breakMinutes = breakTime - 1;

    timer = setInterval(timerFunction, 1000); /* 1000 = 1 SECOND SPEED */
}

/* PAUSE BUTTON */
function pause() {
    isPaused = true;
    clearInterval(timer);
    document.getElementById('start').style.display = 'block';
    document.getElementById('pause').style.display = 'none';
}

/* RESET BUTTON */
function reset() {
    clearInterval(timer);
    isPaused = false;
    workMinutes = workTime - 1;
    breakMinutes = breakTime - 1;
    breakcount = 0;

    /* DISPLAY START/PLAY BUTTON INLY */
    document.getElementById('start').style.display = 'block';
    document.getElementById('reset').style.display = 'none';
    document.getElementById('pause').style.display = 'none';

    /* ADD NAME CLASS IN WORK TITLE */
    worktitle.classList.add('active');
    breaktitle.classList.remove('active');

    document.getElementById('minutes').innerHTML = workTime;
    document.getElementById('seconds').innerHTML = "00";
}

/* TIME FUNCTION TO MOVE THE MINUTES AND SECONDS */
function timerFunction() {
    if (isPaused) return;

    document.getElementById('minutes').innerHTML = workMinutes;
    document.getElementById('seconds').innerHTML = seconds;

    seconds--;

    if(seconds === -1) {
        workMinutes--;

        if(workMinutes === -1) {
            if (breakcount % 2 === 0) {
                workMinutes = breakMinutes;
                breakcount++;

                worktitle.classList.remove('active');
                breaktitle.classList.add('active');

                audio.play();   /* AUDIO FOR END OF WORK */
            } 
            else {
                workMinutes = workTime - 1;
                breakcount++;

                breaktitle.classList.remove('active');
                worktitle.classList.add('active');

                audio2.play();  /* AUDIO FOR END OF BREAK */
            }
        }

        seconds = 59;
    }
}