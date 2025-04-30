var typed = new Typed(".text", {
    strings: ["BERILMU", "BERAKHLAK", "BERWIBAWA"],
    typeSpeed: 100,
    backSpeed: 100,
    backDelay: 1000,
    loop: true
});

var animateButton = function(e) {

	e.preventDefault;
	e.target.classList.remove('animate');
	
	e.target.classList.add('animate');
	setTimeout(function(){
	  e.target.classList.remove('animate');
	},700);
  };
  
  var bubblyButtons = document.getElementsByClassName("bubble-button");
  
  for (var i = 0; i < bubblyButtons.length; i++) {
	bubblyButtons[i].addEventListener('click', animateButton, false);
  }
