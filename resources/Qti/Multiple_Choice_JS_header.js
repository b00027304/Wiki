addoption = function() {
			// clone the last option on the list and increment its id
			//alert('MC JS');
			var newoption = $("#options tr.option:last").clone();
			var oldid = parseInt($("input.optiontext", newoption).attr("id").split("_")[1]);
			var newid = oldid + 1;

			// give it the new id number and wipe its text
			newoption.attr("id", "option_" + newid);
			$("input.optiontext", newoption).attr("id", "option_" + newid + "_optiontext").attr("name", "option_" + newid + "_optiontext").val("").removeClass("error warning");
			$("input.correct", newoption).attr("id", "option_" + newid + "_correct").removeAttr("checked");
			$(".scorecol input", newoption).attr("id", "option_" + newid + "_score").attr("name", "option_" + newid + "_score").val("0").removeClass("error warning");
			if ($("input.correct", newoption).attr("type") == "checkbox")
				$("input.correct", newoption).attr("name", "option_" + newid + "_correct");
			else
				$("input.correct", newoption).attr("value", "option_" + newid);
			$("input.fixed", newoption).attr("id", "option_" + newid + "_fixed").attr("name", "option_" + newid + "_fixed").removeAttr("checked");

			// add the remove and update feedback actions
			$("input.removeoption", newoption).click(removeoption);
			$("input.optiontext", newoption).change(updatefeedback);

			// add it to the list
			$("#options").append(newoption);

			// switch off all non-stimulus tinyMCEs
			removetinymces($("textarea.qtitinymce").not("#stimulus"));

			// clone the last feedback row
			var newfeedback = $("#option_" + oldid + "_feedback").clone();

			// give it and its bits the new id and wipe the text
			newfeedback.attr("id", "option_" + newid + "_feedback");
			$(".feedbackoptiontext", newfeedback).text("");
			$("textarea.feedbackchosen", newfeedback).attr("name", "option_" + newid + "_feedback_chosen").attr("id", "option_" + newid + "_feedback_chosen").val("").removeClass("error warning");
			$("textarea.feedbackunchosen", newfeedback).attr("name", "option_" + newid + "_feedback_unchosen").attr("id", "option_" + newid + "_feedback_unchosen").val("").removeClass("error warning");

			// add the focus actions
			$("textarea.qtitinymce", newfeedback).focus(focustinymce);

			// add it to the list
			$("#feedbackdiv table").append(newfeedback);

			// stripe them correctly
			newoption.add(newfeedback).removeClass("row" + (oldid % 2)).addClass("row" + (newid % 2));
		};

removeoption = function() {
			if ($("#options tr.option").size() < 2) {
				alert("Can't remove the last option");
				return;
			}

			var optionid = $(this).parents("tr:first").attr("id").split("_")[1];

			// switch off all non-stimulus tinyMCEs
			removetinymces($("textarea.qtitinymce").not("#stimulus"));

			$("#option_" + optionid + ", #option_" + optionid + "_feedback").remove();

			// renumber and stripe the remaining options
			var i = 0;
			$("#options tr.option").each(function() {
				$(this).attr("id", "option_" + i);
				$("input.optiontext", this).attr("id", "option_" + i + "_optiontext").attr("name", "option_" + i + "_optiontext");
				$("input.correct", this).attr("id", "option_" + i + "_correct");
				if ($("input.correct", this).attr("type") == "checkbox")
					$("input.correct", this).attr("name", "option_" + i + "_correct");
				else
					$("input.correct", this).attr("name", "correct").attr("value", "option_" + i);
				$("input.fixed", this).attr("id", "option_" + i + "_fixed").attr("name", "option_" + i + "_fixed");
				$(this).removeClass("row" + ((i + 1) % 2)).addClass("row" + (i % 2));
				i++;
			});

			// renumber and stripe the remaining feedback
			i = 0;
			$("#feedbackdiv tr.feedback").each(function() {
				$(this).attr("id", "option_" + i + "_feedback");
				$("textarea.feedbackchosen", this).attr("name", "option_" + i + "_feedback_chosen").attr("id", "option_" + i + "_feedback_chosen");
				$("textarea.feedbackunchosen", this).attr("name", "option_" + i + "_feedback_unchosen").attr("id", "option_" + i + "_feedback_unchosen");
				$(this).removeClass("row" + ((i + 1) % 2)).addClass("row" + (i % 2));
				i++;
			});
		};

toggleshuffle = function() {
			if ($("#shuffle").is(":checked"))
				$("#options th.fixed, #options td.fixed").show();
			else
				$("#options th.fixed, #options td.fixed").hide();
		};

togglefeedback = function() {
			if ($("#feedback").is(":checked"))
				$("#feedbackdiv").show();
			else
				$("#feedbackdiv").hide();
		};

updatefeedback = function() {
			var optionid = $(this).attr("id").split("_")[1];
			$("#option_" + optionid + "_feedback .feedbackoptiontext").text($(this).val());
		};

switchitemtype = function() {
			if ($("input.itemtype:checked").attr("id") == "itemtype_mc") {
				// change to radio buttons
				if ($("#option_0_correct").attr("type") == "radio") return;
				var hadchecked = false;
				$("input.correct").each(function() {
					// remove checked attribute from all but first checked box
					if ($(this).is(":checked")) {
						if (hadchecked)
							$(this).removeAttr("checked");
						else
							hadchecked = true;
					}
					var id = $(this).attr("id");
					var index = id.split("_")[1];
					$(this).removeAttr("id");
					$(this).after('<input type="radio" id="' + id + '" name="correct" value="option_' + index + '" class="correct"' + ($(this).is(":checked") ? ' checked="checked"' : '') + '>');
					$(this).remove();
				});

				// hide choice restriction and scoring options
				$(".choicerestrictions, #scoring").hide();
			} else {
				// change to checkboxes
				if ($("#option_0_correct").attr("type") == "checkbox") return;
				$("input.correct").each(function() {
					var id = $(this).attr("id");
					var index = id.split("_")[1];
					$(this).removeAttr("id");
					$(this).after('<input type="checkbox" id="' + id + '" name="' + id + '" class="correct"' + ($(this).is(":checked") ? ' checked="checked"' : '') + '>');
					$(this).remove();
				});

				// show choice restriction and scoring options
				$(".choicerestrictions, #scoring").show();
			}
		};

switchscoringtype = function() {
			if ($("#scoring input:checked").attr("id") == "scoring_custom") {
				$(".scorecol, #scorerestrictions").show();
				$(".correctcol").hide();
			} else {
				$(".scorecol, #scorerestrictions").hide();
				$(".correctcol").show();
			}
		};

edititemsubmitcheck_itemspecificerrors = function() {
			// if multiple choice, one option must be correct
			if ($("input.itemtype:checked").attr("id") == "itemtype_mc") {
				if ($("input.correct:checked").size() == 0) {
					alert("One response must be marked as correct");
					return false;
				}
			}

			// score restriction options must make sense
			if ($("#scoring input:checked").attr("id") == "scoring_custom") {
				// maximum score
				if ($("#maxscore").val().length != 0 && isNaN($("#maxscore").val())) {
					$.scrollTo($("#maxscore").addClass("error"), scrollduration, scrolloptions);
					alert("Maximum score must be blank or a number");
					return false;
				}

				// minimum score
				if ($("#minscore").val().length != 0 && isNaN($("#minscore").val())) {
					$.scrollTo($("#minscore").addClass("error"), scrollduration, scrolloptions);
					alert("Minimum score must be blank or a number");
					return false;
				}

				// maximum score >= minimum score
				if ($("#maxscore").val() != "" && $("#minscore").val() != "") {
					if (parseFloat($("#minscore").val()) > parseFloat($("#maxscore").val())) {
						$.scrollTo($("#maxscore, #minscore").addClass("error"), scrollduration, scrolloptions);
						alert("Value for minimum score cannot be greater than the value for maximum score");
						return false;
					} else if (parseFloat($("#minscore").val()) == parseFloat($("#maxscore").val())) {
						$.scrollTo($("#maxscore, #minscore").addClass("error"), scrollduration, scrolloptions);
						alert("Minimum and maximum scores can't be equal");
						return false;
					}
				}
			}

			// choice restriction options must make sense
			if ($("input.itemtype:checked").attr("id") == "itemtype_mr") {
				// maximum choices
				if ($("#maxchoices").val().length == 0 || isNaN($("#maxchoices").val())) {
					$.scrollTo($("#maxchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for maximum choices is not a number");
					return false;
				}
				if ($("#maxchoices").val() < 0 || $("#maxchoices").val().indexOf(".") != -1) {
					$.scrollTo($("#maxchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for maximum choices must be zero (no restriction) or a positive integer");
					return false;
				}
				if ($("#maxchoices").val() > $("#options tr.option").size()) {
					$.scrollTo($("#maxchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for maximum choices cannot be greater than the number of possible choices");
					return false;
				}

				// minimum choices
				if ($("#minchoices").val().length == 0 || isNaN($("#minchoices").val())) {
					$.scrollTo($("#minchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for minimum choices is not a number");
					return false;
				}
				if ($("#minchoices").val() < 0 || $("#minchoices").val().indexOf(".") != -1) {
					$.scrollTo($("#minchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for minimum choices must be zero (not require to select any choices) or a positive integer");
					return false;
				}
				if ($("#minchoices").val() > $("#options tr.option").size()) {
					$.scrollTo($("#minchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for minimum choices cannot be greater than the number of possible choices");
					return false;
				}

				// maximum choices >= minimum choices
				if ($("#maxchoices").val() != 0 && $("#minchoices").val() > $("#maxchoices").val()) {
					$.scrollTo($("#maxchoices, #minchoices").addClass("error"), scrollduration, scrolloptions);
					alert("Value for minimum choices cannot be greater than the value for maximum choices");
					return false;
				}
			}
			return true;
		};

edititemsubmitcheck_itemspecificwarnings = function() {
	
			// maximum choices 1 for a multiple response is strange
			if ($("input.itemtype:checked").attr("id") == "itemtype_mr") {
				if ($("#maxchoices").val() == 1) {
					$.scrollTo($("#maxchoices").addClass("warning"), scrollduration, scrolloptions);
					if (!confirm("Value for maximum choices is set as 1 which will lead to radio buttons rather than checkboxes -- click OK to continue regardless or cancel to edit it"))
						return false;
					else
						$("#maxchoices").removeClass("error warning");
				}
			}

			// confirm the user wanted the candidate not to be able to check all 
			// correct responses or to have to check incorrect ones
			if ($("input.itemtype:checked").attr("id") == "itemtype_mr") {
				if ($("#maxchoices").val() != 0 && $("#maxchoices").val() < $("input.correct:checked").size()) {
					$.scrollTo($("#maxchoices").addClass("warning"), scrollduration, scrolloptions);
					if (!confirm("Value for maximum choices is less than the number of correct choices -- click OK to continue regardless or cancel to edit it"))
						return false;
					else
						$("#maxchoices").removeClass("error warning");
				}
				if ($("#minchoices").val() != 0 && $("#minchoices").val() > $("input.correct:checked").size()) {
					$.scrollTo($("#minchoices").addClass("warning"), scrollduration, scrolloptions);
					if (!confirm("Value for minimum choices is greater than the number of correct choices -- click OK to continue regardless or cancel to edit it"))
						return false;
					else
						$("#minchoices").removeClass("error warning");
				}
			}

			// confirm the user wanted an empty question prompt
			if ($("#prompt").val().length == 0) {
				$.scrollTo($("#prompt").addClass("warning"), scrollduration, scrolloptions);
				if (!confirm("Question prompt is empty -- click OK to continue regardless or cancel to edit it"))
					return false;
				else
					$("#prompt").removeClass("error warning");
			}

			// confirm the user wanted any empty boxes
			var ok = true;
			$("input.optiontext").each(function(n) {
				if ($(this).val().length == 0) {
					$.scrollTo($(this).addClass("warning"), scrollduration, scrolloptions);
					ok = confirm("Option " + (n + 1) + " is empty -- click OK to continue regardless or cancel to edit it");
					if (ok)
						$(this).removeClass("error warning");
					else
						return false; //this is "break" in the Jquery each() pseudoloop
				}
			});
			if (!ok) return false;

			// warn about any identical options
			for (var i = 0; i < $("input.optiontext").size(); i++) {
				for (var j = i + 1; j < $("input.optiontext").size(); j++) {
					if ($("#option_" + i + "_optiontext").val() == $("#option_" + j + "_optiontext").val()) {
						$.scrollTo($("#option_" + i + "_optiontext, #option_" + j + "_optiontext").addClass("warning"), scrollduration, scrolloptions);
						ok = confirm("Options " + (i + 1) + " and " + (j + 1) + " are the same -- click OK to continue regardless or cancel to edit them");
						if (ok)
							$("#option_" + i + "_optiontext, #option_" + j + "_optiontext").removeClass("error warning");
						else
							break;
					}
				}
				if (!ok) break;
			}
			if (!ok) return false;

			// confirm the user wanted only one option
			if ($("input.optiontext").size() == 1 && !confirm("There is only one option -- click OK to continue regardless or cancel to add more"))
				return false;

			// confirm it's what the user wanted if shuffle is on but all 
			// options are marked as fixed
			if ($("#shuffle").is(":checked") && $("input.fixed").size() == $("input.fixed:checked").size() && !confirm("Shuffle is selected but all options are marked as fixed -- click OK to continue regardless or cancel to change this"))
				return false;

			// confirm it's what the user wanted if custom scoring is on but all 
			// options have a zero score
			if ($("#scoring input:checked").attr("id") == "scoring_custom") {
				var allzero = true;
				$(".scorecol input").each(function(n) {
					if (parseFloat($(this).val()) != 0) {
						allzero = false;
						return false; //this is "break" in the Jquery each() pseudoloop
					}
				});
				if (allzero) {
					$.scrollTo($(".scorecol input").addClass("warning"), scrollduration, scrolloptions);
					if (confirm("No options are set to give a score -- click OK to continue regardless or cancel to change this"))
						$(".scorecol input").removeClass("error warning");
					else
						return false;
				}
			}

			return true;
		};


$(document).ready(function() {
			$("#addoption").click(addoption);
			$(".removeoption").click(removeoption);
			$("#shuffle").click(toggleshuffle);
			$("#feedback").click(togglefeedback);
			$("input.itemtype").click(switchitemtype);
			$("input.optiontext").change(updatefeedback);
			$("#scoring input").click(switchscoringtype);});