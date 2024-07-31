function preventBack(){
    window.history.forward()
};
setTimeout("preventBack()", 0);
window.onload=function(){
    null;
}