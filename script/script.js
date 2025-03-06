/* SOUND */
const audio = new Audio("./sound/chicken_sound.mp3"); // FILE SOUND
const maxTimeInSeconds = 1; // LIMIT TIME INTO 1 SEC
let startTime = 0;

/* PLAY AUDIO */
function playAudio() {
    audio.currentTime = 0;
    audio.play();

    setTimeout(checkElapsedTime, 100);
}

/* CHECK AUDIO TIME */
function checkElapsedTime() {
    if(!audio.paused && audio.currentTime - startTime >= maxTimeInSeconds) {
        audio.pause();
    } 
    else {
        setTimeout(checkElapsedTime, 100);
    }
}

/* NOTES */
const addBox = document.querySelector(".add-box"), // BUTTON TO SHOW THE FORM
    popupBox = document.querySelector(".popup-box"),
    popupTitle = popupBox.querySelector("header p"),
    closeIcon = popupBox.querySelector("header i"),
    titleTag = popupBox.querySelector("input"), // TITLE
    descTag = popupBox.querySelector("textarea"); // CONTENT

const addUpdateInput = document.getElementById("add_update"); // INPUT BUTTON
var noteForm = document.getElementById("noteForm"); // FORM FOR ADD AND UPDATE

/* POP-UP FORM */
/* ADD NOTES */
addBox.addEventListener("click", () => {
    playAudio(); // AUDIO
    popupTitle.innerText = "Add a New Note"; // BOX TITLE OF ADD NOTE
    addUpdateInput.value = "Add Note"; // TEXT FOR INPUT BUTTON
    noteForm.setAttribute("action", "./query/add.php"); // ADD NOTE FILE
    addUpdateInput.setAttribute("name", "submit"); // NAME OF BUTTON FOR ADD BUTTON
    popupBox.classList.add("show");
    document.querySelector("body").style.overflow = "hidden";
    if (window.innerWidth > 660) titleTag.focus();
});

/* CLOSE BUTTON */
const closePopup = () => {
    titleTag.value = descTag.value = "";
    popupBox.classList.remove("show"); // REMOVE CLASS NAME FROM POPUP BOX
    document.querySelector("body").style.overflow = "auto";
};

closeIcon.addEventListener("click", closePopup);

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        closePopup();
    }
});

/* UPDATE NOTES */
function updateNote(noteId, title, filterDesc) {
    // REPLACE ALL OCCURANCES OF <br/> WITH NEWLINE CHARACTERS
    let description = filterDesc.replaceAll('<br/>', '\n');
    addBox.click();
    titleTag.value = title;
    descTag.value = description;
    popupTitle.innerText = "Note";
    addUpdateInput.value = "Save Note";
    addUpdateInput.setAttribute("name", "submit"); // NAME OF BUTTON FOR UPDATE BUTTON
    noteForm.setAttribute("action", "./query/update.php"); // UPDATE FILE 
    document.getElementById("note_id").value = noteId; // THIS WILL GET THE NOTE_ID PER NOTE TO UPDATE THE NOTE  
}

/* SHOW SETTINGS/MENU */
function showMenu(elem) {
    elem.parentElement.classList.add("show"); // ADD THE CLASS NAME
    document.addEventListener("click", e => {
        if (e.target.tagName != "I" || e.target != elem) {
            elem.parentElement.classList.remove("show"); // REMOVE CLASS NAME
        }
    });
}