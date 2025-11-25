/**
 * Created by bruno.rosa on 14/10/15.
 */
$(document).ready(function(){

    $('h4.marquee').jfontsize({
        btnMinusClasseId: '#jfontsize-m2', // Defines the class or id of the decrease button
        btnDefaultClasseId: '#jfontsize-d2', // Defines the class or id of default size button
        btnPlusClasseId: '#jfontsize-p2', // Defines the class or id of the increase button
        btnMinusMaxHits: 1, // How many times the size can be decreased
        btnPlusMaxHits: 10, // How many times the size can be increased
        sizeChange: 5 // Defines the range of change in pixels
    });

});
