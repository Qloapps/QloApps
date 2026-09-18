/*
 * jPlayer Player Plugin for Popcorn JavaScript Library
 * http://www.jplayer.org
 *
 * Copyright (c) 2013 Happyworm Ltd
 * Licensed under the MIT license.
 * http://opensource.org/licenses/MIT
 *
 * Author: Mark J Panaghiston
 * Version: 1.1.1
 * Date: 5th June 2013
 *
 * For Popcorn Version: 1.3
 * For jPlayer Version: 2.4.0
 * Requires: jQuery 1.3.2+
 * Note: jQuery dependancy cannot be removed since jPlayer 2 is a jQuery plugin. Use of jQuery will be kept to a minimum.
 */

/* Code verified using http://www.jshint.com/ */
/*jshint asi:false, bitwise:false, boss:false, browser:true, curly:false, debug:false, eqeqeq:true, eqnull:false, evil:false, forin:false, immed:false, jquery:true, laxbreak:false, newcap:true, noarg:true, noempty:true, nonew:true, onevar:false, passfail:false, plusplus:false, regexp:false, undef:true, sub:false, strict:false, white:false, smarttabs:true */
/*global Popcorn:false, console:false */

(function(Popcorn) {

	var JQUERY_SCRIPT = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js', // Used if jQuery not already present.
	JPLAYER_SCRIPT = 'http://www.jplayer.org/2.4.0/js/jquery.jplayer.min.js', // Used if jPlayer not already present.
	JPLAYER_SWFPATH = 'http://www.jplayer.org/2.4.0/js/Jplayer.swf', // Used if not specified in jPlayer options via SRC Object.
	SOLUTION = 'html,flash', // The default solution option.
	DEBUG = false, // Decided to leave the debugging option and console output in for the time being. Overhead is trivial.
	jQueryDownloading = false, // Flag to stop multiple instances from each pulling in jQuery, thus corrupting it.
	jPlayerDownloading = false, // Flag to stop multiple instances from each pulling in jPlayer, thus corrupting it.
	format = { // Duplicate of jPlayer 2.4.0 object, to avoid always requiring jQuery and jPlayer to be loaded before performing the _canPlayType() test.
		mp3: {
			codec: 'audio/mpeg; codecs="mp3"',
			flashCanPlay: true,
			media: 'audio'
		},
		m4a: { // AAC / MP4
			codec: 'audio/mp4; codecs="mp4a.40.2"',
			flashCanPlay: true,
			media: 'audio'
		},
		oga: { // OGG
			codec: 'audio/ogg; codecs="vorbis"',
			flashCanPlay: false,
			media: 'audio'
		},
		wav: { // PCM
			codec: 'audio/wav; codecs="1"',
			flashCanPlay: false,
			media: 'audio'
		},
		webma: { // WEBM
			codec: 'audio/webm; codecs="vorbis"',
			flashCanPlay: false,
			media: 'audio'
		},
		fla: { // FLV / F4A
			codec: 'audio/x-flv',
			flashCanPlay: true,
			media: 'audio'
		},
		rtmpa: { // RTMP AUDIO
			codec: 'audio/rtmp; codecs="rtmp"',
			flashCanPlay: true,
			media: 'audio'
		},
		m4v: { // H.264 / MP4
			codec: 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			flashCanPlay: true,
			media: 'video'
		},
		ogv: { // OGG
			codec: 'video/ogg; codecs="theora, vorbis"',
			flashCanPlay: false,
			media: 'video'
		},
		webmv: { // WEBM
			codec: 'video/webm; codecs="vorbis, vp8"',
			flashCanPlay: false,
			media: 'video'
		},
		flv: { // FLV / F4V
			codec: 'video/x-flv',
			flashCanPlay: true,
			media: 'video'
		},
		rtmpv: { // RTMP VIDEO
			codec: 'video/rtmp; codecs="rtmp"',
			flashCanPlay: true,
			media: 'video'
		}
	},
	isObject = function(val) { // Basic check for Object
		if(val && typeof val === 'object' && val.hasOwnProperty) {
			return true;
		} else {
			return false;
		}
	},
	getMediaType = function(url) { // Function to gleam the media type from the URL
		var mediaType = false;
		if(/\.mp3$/i.test(url)) {
			mediaType = 'mp3';
		} else if(/\.mp4$/i.test(url) || /\.m4v$/i.test(url)) {
			mediaType = 'm4v';
		} else if(/\.m4a$/i.test(url)) {
			mediaType = 'm4a';
		} else if(/\.ogg$/i.test(url) || /\.oga$/i.test(url)) {
			mediaType = 'oga';
		} else if(/\.ogv$/i.test(url)) {
			mediaType = 'ogv';
		} else if(/\.webm$/i.test(url)) {
			mediaType = 'webmv';
		}
		return mediaType;
	},
	getSupplied = function(url) { // Function to generate a supplied option from an src object. ie., When supplied not specified.
		var supplied = '',
		separator = '';
		if(isObject(url)) {
			// Generate supplied option from object's properties. Non-format properties would be ignored by jPlayer. Order is unpredictable.
			for(var prop in url) {
				if(url.hasOwnProperty(prop)) {
					supplied += separator + prop;
					separator = ',';
				}
			}
		}
		if(DEBUG) console.log('getSupplied(): Generated: supplied = "' + supplied + '"');
		return supplied;
	};

	Popcorn.player( 'jplayer', {
		_canPlayType: function( containerType, url ) {
			// url : Either a String or an Object structured similar a jPlayer media object. ie., As used by setMedia in jPlayer.
			// The url object may also contain a solution and supplied property.

			// Define the src object structure here!

			var cType = containerType.toLowerCase(),
			srcObj = {
				media:{},
				options:{}
			},
			rVal = false, // Only a boolean false means it is not supported.
			mediaType;

			if(cType !== 'video' && cType !== 'audio') {

				if(typeof url === 'string') {
					// Check it starts with http, so the URL is absolute... Well, it is not a perfect check.
					if(/^http.*/i.test(url)) {
						mediaType = getMediaType(url);
						if(mediaType) {
							srcObj.media[mediaType] = url;
							srcObj.options.solution = SOLUTION;
							srcObj.options.supplied = mediaType;
						}
					}
				} else {
					srcObj = url; // Assume the url is an src object.
				}

				// Check for Object and appropriate minimum data structure.
				if(isObject(srcObj) && isObject(srcObj.media)) {

					if(!isObject(srcObj.options)) {
						srcObj.options = {};
					}

					if(!srcObj.options.solution) {
						srcObj.options.solution = SOLUTION;
					}

					if(!srcObj.options.supplied) {
						srcObj.options.supplied = getSupplied(srcObj.media);
					}

					// Figure out how jPlayer will play it.
					// This may not work properly when both audio and video is supplied. ie., A media player. But it should return truethy and jPlayer can figure it out.
					
					var solution = srcObj.options.solution.toLowerCase().split(","), // Create the solution array, with prority based on the order of the solution string.
					supplied = srcObj.options.supplied.toLowerCase().split(","); // Create the supplied formats array, with prority based on the order of the supplied formats string.

					for(var sol = 0; sol < solution.length; sol++) {

						var solutionType = solution[sol].replace(/^\s+|\s+$/g, ""), //trim
						checkingHtml = solutionType === 'html',
						checkingFlash = solutionType === 'flash',
						mediaElem;

						for(var fmt = 0; fmt < supplied.length; fmt++) {
							mediaType = supplied[fmt].replace(/^\s+|\s+$/g, ""); //trim
							if(format[mediaType]) { // Check format is valid.

								// Create an HTML5 media element for the type of media.
								if(!mediaElem && checkingHtml) {
									mediaElem = document.createElement(format[mediaType].media);
								}
								// See if the HTML5 media element can play the MIME / Codec type.
								// Flash also returns the object if the format is playable, so it is truethy, but that html property is false.
								// This assumes Flash is available, but that should be dealt with by jPlayer if that happens.
								var htmlCanPlay = !!(mediaElem && mediaElem.canPlayType && mediaElem.canPlayType(format[mediaType].codec)),
								htmlWillPlay = htmlCanPlay && checkingHtml,
								flashWillPlay = format[mediaType].flashCanPlay && checkingFlash;
								// The first one found will match what jPlayer uses.
								if(htmlWillPlay || flashWillPlay) {
									rVal = {
										html: htmlWillPlay,
										type: mediaType
									};
									sol = solution.length; // Exit solution loop
									fmt = supplied.length; // Exit supplied loop
								}
							}
						}
					}
				}
			}
			return rVal;
		},
		// _setup: function( options ) { // Warning: options is deprecated.
		_setup: function() {
			var media = this,
			myPlayer, // The jQuery selector of the jPlayer element. Usually a <div>
			jPlayerObj, // The jPlayer data instance. For performance and DRY code.
			mediaType = 'unknown',
			jpMedia = {},
			jpOptions = {},
			ready = false, // Used during init to override the annoying duration dependance in the track event padding during Popcorn's isReady(). ie., We is ready after loadeddata and duration can then be set real value at leisure.
			duration = 0, // For the durationchange event with both HTML5 and Flash solutions. Used with 'ready' to keep control during the Popcorn isReady() via loadeddata event. (Duration=0 is bad.)
			durationchangeId = null, // A timeout ID used with delayed durationchange event. (Because of the duration=NaN fudge to avoid Popcorn track event corruption.)
			canplaythrough = false,
			error = null, // The MediaError object.

			dispatchDurationChange = function() {
				if(ready) {
					if(DEBUG) console.log('Dispatched event : durationchange : ' + duration);
					media.dispatchEvent('durationchange');
				} else {
					if(DEBUG) console.log('DELAYED EVENT (!ready) : durationchange : ' + duration);
					clearTimeout(durationchangeId); // Stop multiple triggers causing multiple timeouts running in parallel.
					durationchangeId = setTimeout(dispatchDurationChange, 250);
				}
			},

			jPlayerFlashEventsPatch = function() {

				/* Events already supported by jPlayer Flash:
				 * loadstart
				 * loadedmetadata (M4A, M4V)
				 * progress
				 * play
				 * pause
				 * seeking
				 * seeked
				 * timeupdate
				 * ended
				 * volumechange
				 * error <- See the custom handler in jPlayerInit()
				 */

				/* Events patched:
				 * loadeddata
				 * durationchange
				 * canplaythrough
				 * playing
				 */

				/* Events NOT patched:
				 * suspend
				 * abort
				 * emptied
				 * stalled
				 * loadedmetadata (MP3)
				 * waiting
				 * canplay
				 * ratechange
				 */

				// Triggering patched events through the jPlayer Object so the events are homogeneous. ie., The contain the event.jPlayer data structure.

				var checkDuration = function(event) {
					if(event.jPlayer.status.duration !== duration) {
						duration = event.jPlayer.status.duration;
						dispatchDurationChange();
					}
				},

				checkCanPlayThrough = function(event) {
					if(!canplaythrough && event.jPlayer.status.seekPercent === 100) {
						canplaythrough = true;
						setTimeout(function() {
							if(DEBUG) console.log('Trigger : canplaythrough');
							jPlayerObj._trigger($.jPlayer.event.canplaythrough);
						}, 0);
					}
				};

				myPlayer.bind($.jPlayer.event.loadstart, function() {
					setTimeout(function() {
						if(DEBUG) console.log('Trigger : loadeddata');
						jPlayerObj._trigger($.jPlayer.event.loadeddata);
					}, 0);
				})
				.bind($.jPlayer.event.progress, function(event) {
					checkDuration(event);
					checkCanPlayThrough(event);
				})
				.bind($.jPlayer.event.timeupdate, function(event) {
					checkDuration(event);
					checkCanPlayThrough(event);
				})
				.bind($.jPlayer.event.play, function() {
					setTimeout(function() {
						if(DEBUG) console.log('Trigger : playing');
						jPlayerObj._trigger($.jPlayer.event.playing);
					}, 0);
				});

				if(DEBUG) console.log('Created CUSTOM event handlers for FLASH');
			},

			jPlayerInit = function() {
				(function($) {

					myPlayer = $('#' +  media.id);

					if(typeof media.src === 'string') {
						mediaType = getMediaType(media.src);
						jpMedia[mediaType] = media.src;
						jpOptions.supplied = mediaType;
						jpOptions.solution = SOLUTION;
					} else if(isObject(media.src)) {
						jpMedia = isObject(media.src.media) ? media.src.media : {};
						jpOptions = isObject(media.src.options) ? media.src.options : {};
						jpOptions.solution = jpOptions.solution || SOLUTION;
						jpOptions.supplied = jpOptions.supplied || getSupplied(media.src.media);
					}

					// Allow the swfPath to be set to local server. ie., If the jPlayer Plugin is local and already on the page, then you can also use the local SWF.
					jpOptions.swfPath = jpOptions.swfPath || JPLAYER_SWFPATH;

					myPlayer.bind($.jPlayer.event.ready, function(event) {
						if(event.jPlayer.flash.used) {
							jPlayerFlashEventsPatch();
						}
						// Set the media andd load it, so that the Flash solution behaves similar to HTML5 solution.
						// This also allows the loadstart event to be used to know jPlayer is ready.
						$(this).jPlayer('setMedia', jpMedia).jPlayer('load');
					});

					// Do not auto-bubble the reserved events, nor the loadeddata and durationchange event, since the duration must be carefully handled when loadeddata event occurs.
					// See the duration property code for more details. (Ranting.)

					var reservedEvents = $.jPlayer.reservedEvent + ' loadeddata durationchange',
					reservedEvent = reservedEvents.split(/\s+/g);

					// Generate event handlers for all the standard HTML5 media events. (Except durationchange)

					var bindEvent = function(name) {
						myPlayer.bind($.jPlayer.event[name], function(event) {
							if(DEBUG) console.log('Dispatched event: ' + name + (event && event.jPlayer ? ' (' + event.jPlayer.status.currentTime + 's)' : '')); // Must be after dispatch for some reason on Firefox/Opera
							media.dispatchEvent(name);
						});
						if(DEBUG) console.log('Created event handler for: ' + name);
					};

					for(var eventName in $.jPlayer.event) {
						if($.jPlayer.event.hasOwnProperty(eventName)) {
							var nativeEvent = true;
							for(var iRes in reservedEvent) {
								if(reservedEvent.hasOwnProperty(iRes)) {
									if(reservedEvent[iRes] === eventName) {
										nativeEvent = false;
										break;
									}
								}
							}
							if(nativeEvent) {
								bindEvent(eventName);
							} else {
								if(DEBUG) console.log('Skipped auto event handler creation for: ' + eventName);
							}
						}
					}

					myPlayer.bind($.jPlayer.event.loadeddata, function(event) {
						if(DEBUG) console.log('Dispatched event: loadeddata' + (event && event.jPlayer ? ' (' + event.jPlayer.status.currentTime + 's)' : ''));
						media.dispatchEvent('loadeddata');
						ready = true;
					});
					if(DEBUG) console.log('Created CUSTOM event handler for: loadeddata');

					myPlayer.bind($.jPlayer.event.durationchange, function(event) {
						duration = event.jPlayer.status.duration;
						dispatchDurationChange();
					});
					if(DEBUG) console.log('Created CUSTOM event handler for: durationchange');

					// The error event is a special case. Plus jPlayer error event assumes it is a broken URL. (It could also be a decoder error... Or aborted or a Network error.)
					myPlayer.bind($.jPlayer.event.error, function(event) {
						// Not sure how to handle the error situation. Popcorn does not appear to have the error or error.code property documented here: http://popcornjs.org/popcorn-docs/media-methods/
						// If any error event happens, then something has gone pear shaped.

						error = event.jPlayer.error; // Saving object pointer, not a copy of the object. Possible garbage collection issue... But the player is dead anyway, so don't care.

						if(error.type === $.jPlayer.error.URL) {
							error.code = 4; // MEDIA_ERR_SRC_NOT_SUPPORTED since jPlayer makes this assumption. It is the most common error, then the decode error. Never seen either of the other 2 error types occur.
						} else {
							error.code = 0; // It was a jPlayer error, not an HTML5 media error.
						}

						if(DEBUG) console.log('Dispatched event: error');
						if(DEBUG) console.dir(error);
						media.dispatchEvent('error');
					});
					if(DEBUG) console.log('Created CUSTOM event handler for: error');

					Popcorn.player.defineProperty( media, 'error', {
						set: function() {
							// Read-only property
							return error;
						},
						get: function() {
							return error;
						}
					});

					Popcorn.player.defineProperty( media, 'currentTime', {
						set: function( val ) {
							if(jPlayerObj.status.paused) {
								myPlayer.jPlayer('pause', val);
							} else {
								myPlayer.jPlayer('play', val);
							}
							return val;
						},
						get: function() {
							return jPlayerObj.status.currentTime;
						}
					});

					/* The joy of duration and the loadeddata event isReady() handler
					 * The duration is assumed to be a NaN or a valid duration.
					 * jPlayer uses zero instead of a NaN and this screws up the Popcorn track event start/end arrays padding.
					 * This line here:
					 *  videoDurationPlus = duration != duration ? Number.MAX_VALUE : duration + 1;
					 * Not sure why it is not simply:
					 *  videoDurationPlus = Number.MAX_VALUE; // Who cares if the padding is close to the real duration?
					 * So if you trigger loadeddata before the duration is correct, the track event padding is screwed up. (It pads the start, not the end... Well, duration+1 = 0+1 = 1s)
					 * That line makes the MP3 Flash fallback difficult to setup. The whole MP3 will need to load before the duration is known.
					 * Planning on using a NaN for duration until a >0 value is found... Except with MP3, where seekPercent must be 100% before setting the duration.
					 * Why not just use a NaN during init... And then correct the duration later?
					 */

					Popcorn.player.defineProperty( media, 'duration', {
						set: function() {
							// Read-only property
							if(ready) {
								return duration;
							} else {
								return NaN;
							}
						},
						get: function() {
							if(ready) {
								return duration; // Popcorn has initialized, we can now use duration zero or whatever without fear.
							} else {
								return NaN; // Keep the duration a NaN until after loadeddata event has occurred. Otherwise Popcorn track event padding is corrupted.
							}
						}
					});

					Popcorn.player.defineProperty( media, 'muted', {
						set: function( val ) {
							myPlayer.jPlayer('mute', val);
							return jPlayerObj.options.muted;
						},
						get: function() {
							return jPlayerObj.options.muted;
						}
					});

					Popcorn.player.defineProperty( media, 'volume', {
						set: function( val ) {
							myPlayer.jPlayer('volume', val);
							return jPlayerObj.options.volume;
						},
						get: function() {
							return jPlayerObj.options.volume;
						}
					});

					Popcorn.player.defineProperty( media, 'paused', {
						set: function() {
							// Read-only property
							return jPlayerObj.status.paused;
						},
						get: function() {
							return jPlayerObj.status.paused;
						}
					});

					media.play = function() {
						myPlayer.jPlayer('play');
					};
					media.pause = function() {
						myPlayer.jPlayer('pause');
					};

					myPlayer.jPlayer(jpOptions); // Instance jPlayer. Note that the options should not have a ready event defined... Kill it by default?
					jPlayerObj = myPlayer.data('jPlayer');

				}(jQuery));
			},

			jPlayerCheck = function() {
				if (!jQuery.jPlayer) {
					if (!jPlayerDownloading) {
						jPlayerDownloading = true;
						Popcorn.getScript(JPLAYER_SCRIPT, function() {
							jPlayerDownloading = false;
							jPlayerInit();
						});
					} else {
						setTimeout(jPlayerCheck, 250);
					}
				} else {
					jPlayerInit();
				}
			},

			jQueryCheck = function() {
				if (!window.jQuery) {
					if (!jQueryDownloading) {
						jQueryDownloading = true;
						Popcorn.getScript(JQUERY_SCRIPT, function() {
							jQueryDownloading = false;
							jPlayerCheck();
						});
					} else {
						setTimeout(jQueryCheck, 250);
					}
				} else {
					jPlayerCheck();
				}
			};

			jQueryCheck();
		},
		_teardown: function() {
			jQuery('#' +  this.id).jPlayer('destroy');
		}
	});

}(Popcorn));