getgapstrings = function() {
			var value = $("#textbody").val();
			var pos = 0;
			var endpos;
			var gaps = [];

			while (true) {
				pos = value.indexOf("[", pos);
				if (pos == -1)
					break;
				endpos = value.indexOf("]", pos);
				if (endpos == -1)
					break;

				gaps[gaps.length] = value.substring(pos + 1, endpos);

				pos = endpos;
			}
			return gaps;
		};

		updatetextgap = function() {
			var gapid = parseInt($(this).parents("div.gap:first").attr("id").split("_")[1]);
			var gapstrings = getgapstrings();
			if (gapid > gapstrings.length - 1) {
				console.error("trying to update gapstring which doesn't exist");
				return;
			}

			var value = $("#textbody").val();
			var pos = -1;
			var endpos;
			var gap = -1;
			while (gap < gapid) {
				pos++;
				pos = value.indexOf("[", pos);
				if (pos == -1) {
					console.error("didn't find next gap");
					return;
				}
				endpos = value.indexOf("]", pos);
				if (endpos == -1) {
					console.error("didn't find end of gap");
					return;
				}
				gap++;
			}

			$("#textbody").val(value.substring(0, pos + 1) + $("#gap_" + gapid + " input.responsetext:first").val() + value.substring(endpos));
		};

		getfirstresponsestrings = function() {
			var strings = [];
			$("table.responses:visible").each(function() {
				strings[strings.length] = $("input.responsetext:first", this).val();
			});
			return strings;
		};

		updategapstable = function() {
			var currentgap = 0;

			while (true) {
				var gapstrings = getgapstrings();
				var firstresponsestrings = getfirstresponsestrings();

				// finished if current gap doesn't exist
				if (currentgap >= gapstrings.length)
					break;

				// match gaps in text to table gaps, in order
				var matches = [];
				var prev = -1;
				for (var textgap = 0; textgap < gapstrings.length; textgap++) {
					for (var gaptable = prev + 1; gaptable < firstresponsestrings.length; gaptable++) {
						if (gapstrings[textgap] == firstresponsestrings[gaptable]) {
							matches[matches.length] = [textgap, gaptable];
							prev = textgap;
							break;
						}
					}
				}

				// consider the first match from the current gap
				var match = undefined;
				for (var i = 0; i < matches.length; i++)
					if (matches[i][0] >= currentgap)
						match = matches[i];

				// if no more matches, correct remaining gaps
				if (match == undefined)
					break;

				// go through the textgap/gaptable pairs from current gap to 
				// current match
				for (var gap = currentgap; gap < match[0] && gap < match[1]; gap++) {
					// update the first response in the table to the contents of 
					// the text gap
					$("#gap_" + gap + "_response_0").val(gapstrings[gap]);
				}

				if (match[0] == match[1]) {
					// nothing needs to be added or deleted
					currentgap = match[0] + 1;
					continue;
				}

				if (gap == match[1]) {
					// there are extra gaps in the text -- add tables in reverse 
					// order at this position
					for (var i = match[0]; i > match[1]; i--)
						addgap(match[1], gapstrings[i - 1]);
					currentgap = match[0] + 1;
					continue;
				}

				if (gap == match[0]) {
					// there are extra gap tables -- delete the extras
					for (var i = match[0]; i < match[1]; i++)
						$("#gap_" + i).remove();
					renumber();
					currentgap = match[0] + 1;
					continue;
				}
			}

			// we're after the last match now -- all that's left is to 
			// correct/add/delete tables for any gaps after the last match to 
			// simplify logic just treat all of them as after the last match

			var gapstrings = getgapstrings();
			var firstresponsestrings = getfirstresponsestrings();

			// correct number of tables
			for (var i = firstresponsestrings.length; i < gapstrings.length; i++)
				addgap(i, gapstrings[i]);
			for (var i = gapstrings.length - 1; i < firstresponsestrings.length - 1; i++)
				$("#gap_" + (i + 1)).remove();

			// update first responses
			for (var i = 0; i < gapstrings.length; i++)
				$("#gap_" + i + "_response_0").val(gapstrings[i]);
		};

		addgap = function(newid, response) {
			// clone the template gap
			var newgap = $("#gap_-1").clone();

			// give it and its bits the new id number
			$("input.responsetext", newgap).val(response).change(updatetextgap);

			// reinstate the add action
			$("input.addresponse", newgap).click(addresponse);

			// make it visible
			newgap.show();

			// add it to the list in place
			$("#gap_" + (newid - 1)).after(newgap);

			// renumber everything
			renumber();

			return newid;
		};

		removeresponse = function() {
			// get our gap and its id
			var gap = $(this).parents("div.gap:first");
			var gapid = gap.attr("id").split("_")[1];

			// can't delete the last response
			if ($("table.responses tr.response", gap).size() < 2) {
				alert("Can't remove the only response");
				return;
			}

			$(this).parents("tr:first").remove();

			// renumber everything
			renumber();
		};

		addresponse = function() {
			// get our gap and its id
			var gap = $(this).parents("div.gap:first");
			var gapid = gap.attr("id").split("_")[1];

			// get the new response id
			var newid = parseInt($("table.responses tr.response:last input.responsetext", gap).attr("id").split("_")[3]) + 1;

			// clone the template response and update the ids
			var newresponse = $("#gap_-1 table.responses tr.response:first").clone();

			// reinstate the remove action and make it visible
			$("input.removeresponse", newresponse).click(removeresponse).show();

			// add the new row to the table
			$("table.responses", gap).append(newresponse);

			// renumber everything
			renumber();
		};

		renumber = function() {
			var gapid = -1; // include template gap
			$("div.gap").each(function() {
				$(this).attr("id", "gap_" + gapid);
				$("span.gapnumber", this).html(gapid + 1);
				var responseid = 0;
				$("table.responses tr.response", this).each(function() {
					$("input.responsetext", this).attr("id", "gap_" + gapid + "_response_" + responseid).attr("name", "gap_" + gapid + "_response_" + responseid);
					$("input.responsescore", this).attr("id", "gap_" + gapid + "_response_" + responseid + "_score").attr("name", "gap_" + gapid + "_response_" + responseid + "_score");
					$(this).removeClass("row" + ((responseid + 1) % 2)).addClass("row" + (responseid % 2));
					responseid++;
				});
				gapid++;
			});
		};

		edititemsubmitcheck_pre = function() {
			// ensure the gaps table is up to date
			updategapstable();
		};

		edititemsubmitcheck_itemspecificerrors = function() {
			// must have at least one gap
			if ($("div.gap:visible").size() == 0) {
				$.scrollTo($("#textbody").addClass("error"), scrollduration, scrolloptions);
				alert("You must have at least one gap for the candidate to fill in");
				return false;
			}

			// scores must make sense
			var ok = true;
			$("input.responsescore:visible").each(function() {
				if ($(this).val().length == 0 || isNaN($(this).val())) {
					var gapid = parseInt($(this).attr("id").split("_")[1]);
					var responseid = parseInt($(this).attr("id").split("_")[3]);
					$.scrollTo($(this).addClass("error"), scrollduration, scrolloptions);
					alert("Score for gap " + (gapid + 1) + " response " + (responseid + 1) + " must be a number");
					ok = false;
					return false;
				}
			});
			if (!ok) return false;

			// can't have empty responses
			$("input.responsetext:visible").each(function(n) {
				if ($(this).val().length == 0) {
					var gapid = parseInt($(this).attr("id").split("_")[1]);
					var responseid = parseInt($(this).attr("id").split("_")[3]);
					$.scrollTo($(this).addClass("error"), scrollduration, scrolloptions);
					alert("Gap " + (gapid + 1) + " response " + (responseid + 1) + " is empty -- this is not allowed");
					ok = false;
					return false; //this is "break" in the Jquery each() pseudoloop
				}
			});
			if (!ok) return false;

			// can't have identical responses for a single gap
			for (var gap = 0; gap < $("div.gap:visible").size(); gap++) {
				for (var i = 0; i < $("#gap_" + gap + " input.responsetext").size(); i++) {
					for (var j = i + 1; j < $("#gap_" + gap + " input.responsetext").size(); j++) {
						if ($("#gap_" + gap + "_response_" + i).val() == $("#gap_" + gap + "_response_" + j).val()) {
							$.scrollTo($("#gap_" + gap + "_response_" + i + ", #gap_" + gap + "_response_" + j).addClass("error"), scrollduration, scrolloptions);
							alert("No two responses can be the same but gap " + (gap + 1) + " responses " + (i + 1) + " and " + (j + 1) + " are equal");
							return false;
						}
					}
				}
			};

			return true;
		};

		edititemsubmitcheck_itemspecificwarnings = function() {
			// confirm the user wanted zero scores
			var ok = true;
			$("input.responsescore:visible").each(function(n) {
				if (parseFloat($(this).val()) == 0.0) {
					var gapid = parseInt($(this).attr("id").split("_")[1]);
					var responseid = parseInt($(this).attr("id").split("_")[3]);
					$.scrollTo($(this).addClass("warning"), scrollduration, scrolloptions);
					ok = confirm("Score for gap " + (gapid + 1) + " response " + (responseid + 1) + " is zero but this is the default score for any response not listed -- click OK to continue regardless or cancel to edit it");
					if (ok)
						$(this).removeClass("error warning");
					else
						return false; //this is "break" in the Jquery each() pseudoloop
				}
			});
			if (!ok) return false;

			return true;
		};

		$(document).ready(function() {
			$("#textbody").change(updategapstable);
			$("input.addresponse:visible").click(addresponse);
			$("input.removeresponse:visible").click(removeresponse);
			$("input.responsetext:visible").change(updatetextgap);
		});