(function () {
	var sections = document.querySelectorAll( '.elementor-top-section' );
	if ( ! sections.length ) {
		return;
	}
	sections.forEach( function ( el ) {
		el.classList.add( 'tt-reveal' );
	} );
	var io = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( entry.isIntersecting ) {
				entry.target.classList.add( 'tt-in' );
				io.unobserve( entry.target );
			}
		} );
	}, { threshold: 0.12 } );
	sections.forEach( function ( el ) {
		io.observe( el );
	} );
} )();
