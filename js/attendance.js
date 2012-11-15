
/*
 * onclick method to select table row
 */
function selectRow(row)
{
    var firstInput = row.getElementsByTagName('input')[0];

    firstInput.checked = !firstInput.checked;
    if (firstInput.checked)
    {
	row.className="present";
    }
    else
    {
	row.className="absent";
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
}
