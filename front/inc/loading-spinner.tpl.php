<?php
//silance
if (!defined('ABSPATH')) exit;
?>
<div class="wpmg-spinner"></div>
<style>
.wpmg-wrap.wpmg-loading .wpmg-container {
    opacity: 0.6;
}
.wpmg-wrap {
    position: relative;
}
.wpmg-wrap.wpmg-loading:before {
    content: "";
    background: rgb(0 0 0 / 78%);
    width: 100%;
    height: 100%;
    position: absolute;
    z-index: 9;
}
.wpmg-wrap.wpmg-loading .wpmg-spinner{
    display: block;
}
.wpmg-spinner {
	width: 40px;
	height: 40px;
	background-color: #fff;
	position: absolute;
	-webkit-animation: sk-rotateplane 1.2s infinite ease-in-out;
	animation: sk-rotateplane 1.2s infinite ease-in-out;
    left: 50%;
    top: 50%;
    z-index: 999;
    margin-left: -20px;
    margin-top: -20px;
    display: none;
}

@-webkit-keyframes sk-rotateplane {
	0% { -webkit-transform: perspective(120px) }
	50% { -webkit-transform: perspective(120px) rotateY(180deg) }
	100% { -webkit-transform: perspective(120px) rotateY(180deg)  rotateX(180deg) }
}

@keyframes sk-rotateplane {
  0% { 
    transform: perspective(120px) rotateX(0deg) rotateY(0deg);
    -webkit-transform: perspective(120px) rotateX(0deg) rotateY(0deg) 
  } 50% { 
    transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);
    -webkit-transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg) 
  } 100% { 
    transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
    -webkit-transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
  }
}
</style>