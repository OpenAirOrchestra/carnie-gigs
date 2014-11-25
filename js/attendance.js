
/*
 * onclick method to select table row
 */
function selectRow(row)
{
    var inputs = row.getElementsByTagName('input');
    var firstInput = inputs[0];

    firstInput.checked = !firstInput.checked;
    if (firstInput.checked)
    {
	row.className="present";
    }
    else
    {
	row.className="absent";
    }

    // Remove "disabled" from child inputs, somthing has changed
    for (var i = 0; i < inputs.length; i++) {
	inputs[i].disabled = false;
    }
}

function checkClicked(checkbox, event)
{
    if (event.cancelBubble)
    {
        event.cancelBubble = true;
    }
    else
    {
        event.stopPropagation();
    }

    var row = checkbox.parentNode.parentNode;
    if (checkbox.checked)
    {
	row.className="present";
    }
    else
    {
	row.className="absent";
    }

    // Remove "disabled" from child inputs, somthing has changed
    var inputs = row.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
	inputs[i].disabled = false;
    }
}
