//// TRACE
function trace(message, color, size) {
	if (!color){
		color='#d22d3c';
	}
	if (!size){
		size=10;
	}
	console.log('%c'+message,'background-color:'+color+'; color: #ffffff; font-size: '+size+'px; padding:5px 10px;');
}
