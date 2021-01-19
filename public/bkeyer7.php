<?php
$pitchMin = 300;
$pitchMax = 1100;
$speedMin = 5;
$speedMax = 20;
if ($_POST["MorseCode"] ?? false) {
    $myPitch = isset($_POST["myPitch"]) ? $_POST["myPitch"] : "550";
    $mySpeed = isset($_POST["mySpeed"]) ? $_POST["mySpeed"] : "10";

    //delete all unwanted characters in input string
    $m_get = strtoupper(preg_replace('/[^a-zA-Z0-9ßäÄöÖüÜ() +=]/si','',$_POST["MorseCode"]));

    //prepare caption for Morse ID
    $m_caption = "ID = "."<b>".str_replace("(E)","",str_replace("=","",$m_get));
    $m_in = "";
    $daid = false;
    $emergency = false;
    if (strlen($m_get) == 0) {
        $m_caption = "";
        $m_code = "";
        $m_negativ = "";
        $m_pos_play = "";
        $m_neg_play = "";
    } else {
        //delete unneeded leading and trailing spaces
        $m_get = ltrim($m_get);
        $m_get = rtrim($m_get);

        //search for "=" = "DAID"
        if ((strpos($m_get,"=") > 0) AND (strpos($m_get,"=") == strlen($m_get) - 1)) {
            $daid = true;
        }
        $m_get = rtrim(str_replace("=","",$m_get));

        //search for "(e)" = "EMERGENCY"
        if ((strpos($m_get,"(E)") > 0) AND (strpos($m_get,"(E)") == strlen($m_get) - 3)) {
            $emergency = true;
        }
        $m_get = rtrim(str_replace("(E)","",$m_get));

        //replace existing spaces with "#" for proper word spacing
        $m_get = str_replace(" ","#",$m_get);

        //add blanks for proper character spacing
        for ($i = 0; $i < strlen($m_get); $i++) {
            $m_in .= substr($m_get,$i,1)." ";
        }

        //delete unnecessary blanks
        $m_in = str_replace(" # ","#",$m_in);
        $m_in = str_replace(" + ","+",$m_in);

        //convert ID to Morse code
        $m_code = morse_encoder($m_in);

        //adjust caption and add Emergency-"e" to Morse-code
        if ($emergency == true) {
            $m_caption .= "[e]</b>";
            $m_code .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"."_"."&nbsp;&nbsp;&nbsp;";
        }

        //adjust caption and Morse-code for DAID
        if ($daid == true) {
            $m_caption .= "[DAID]</b>";
            $m_code =
                "&nbsp;&nbsp;&nbsp;"
                . $m_code
                . "_____________?_____________"
                . "&nbsp;&nbsp;&nbsp;"
                . $m_code
                . "_____________?_____________";
        } else {
            $m_caption .= "</b>";
            $m_code =
                $m_code
                . str_repeat("&nbsp;",10)
                . "?"
                . str_repeat("&nbsp;",13)
                . $m_code
                . str_repeat("&nbsp;",10)
                . "?"
                . str_repeat("&nbsp;",13);
        }

        //delete unneeded blanks
        $m_code = str_replace("$ ","$",$m_code);

        //convert Morse-code to negative
        $m_negativ = str_replace("&nbsp;","|",$m_code);
        $m_negativ = str_replace(" ","|",$m_negativ);
        $m_negativ = str_replace("_","&nbsp;",$m_negativ);
        $m_negativ = str_replace("|","_",$m_negativ);

        //Build positive ID for WebAudio Player (# = SAID, | = DAID)
        if ($daid == true) {
            $m_pos_play = str_replace(
            "_____________?_____________", "|", $m_code
            );
        } else {
            $m_pos_play = str_replace(str_repeat("&nbsp;",10) . "?" . str_repeat("&nbsp;",13),"#", $m_code);
        }
        $m_pos_play = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","$", $m_pos_play);
        $m_pos_play = str_replace("&nbsp;&nbsp;&nbsp;","=", $m_pos_play);
        $m_pos_play = str_replace("___","-", $m_pos_play);
        $m_pos_play = str_replace("_",".", $m_pos_play);
        $m_pos_play = str_replace("&nbsp;","", $m_pos_play);
        $m_pos_play = str_replace("="," ", $m_pos_play);


        //Build negative ID for WebAudio Player
        if ($daid == true) {
            $m_neg_play = str_replace(str_repeat("&nbsp;",10)."?".str_repeat("&nbsp;",13),"#",$m_negativ);
        } else {
            $m_neg_play = str_replace("_____________?_____________","|",$m_negativ);
        }
        $m_neg_play = str_replace("__________","& ",$m_neg_play);
        $m_neg_play = str_replace("&nbsp;&nbsp;&nbsp;","=",$m_neg_play);
        $m_neg_play = str_replace("___","-",$m_neg_play);
        $m_neg_play = str_replace("_",".",$m_neg_play);
        $m_neg_play = str_replace("&nbsp;","",$m_neg_play);
        $m_neg_play = str_replace("="," ",$m_neg_play);
        $m_neg_play = str_replace("$","&",$m_neg_play);
    }
} else {
    $myPitch = "550";
    $mySpeed = "10";
}

function get_morse()
{
    return [
        " " => "&nbsp;&nbsp;&nbsp;",
        "#" => "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
        "$" => "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
        "+" => "&nbsp;",
        "A" => "_&nbsp;___",
        "B" => "___&nbsp;_&nbsp;_&nbsp;_",
        "C" => "___&nbsp;_&nbsp;___&nbsp;_",
        "D" => "___&nbsp;_&nbsp;_",
        "E" => "_",
        "F" => "_&nbsp;_&nbsp;___&nbsp;_",
        "G" => "___&nbsp;___&nbsp;_",
        "H" => "_&nbsp;_&nbsp;_&nbsp;_",
        "I" => "_&nbsp;_",
        "J" => "_&nbsp;___&nbsp;___&nbsp;___",
        "K" => "___&nbsp;_&nbsp;___",
        "L" => "_&nbsp;___&nbsp;_&nbsp;_",
        "M" => "___&nbsp;___",
        "N" => "___&nbsp;_",
        "O" => "___&nbsp;___&nbsp;___",
        "P" => "_&nbsp;___&nbsp;___&nbsp;_",
        "Q" => "___&nbsp;___&nbsp;_&nbsp;___",
        "R" => "_&nbsp;___&nbsp;_",
        "S" => "_&nbsp;_&nbsp;_",
        "T" => "___",
        "U" => "_&nbsp;_&nbsp;___",
        "V" => "_&nbsp;_&nbsp;_&nbsp;___",
        "W" => "_&nbsp;___&nbsp;___",
        "X" => "___&nbsp;_&nbsp;_&nbsp;___",
        "Y" => "___&nbsp;_&nbsp;___&nbsp;___",
        "Z" => "___&nbsp;___&nbsp;_&nbsp;_",
        "1" => "_&nbsp;___&nbsp;___&nbsp;___&nbsp;___",
        "2" => "_&nbsp;_&nbsp;___&nbsp;___&nbsp;___",
        "3" => "_&nbsp;_&nbsp;_&nbsp;___&nbsp;___",
        "4" => "_&nbsp;_&nbsp;_&nbsp;_&nbsp;___",
        "5" => "_&nbsp;_&nbsp;_&nbsp;_&nbsp;_",
        "6" => "___&nbsp;_&nbsp;_&nbsp;_&nbsp;_",
        "7" => "___&nbsp;___&nbsp;_&nbsp;_&nbsp;_",
        "8" => "___&nbsp;___&nbsp;___&nbsp;_&nbsp;_",
        "9" => "___&nbsp;___&nbsp;___&nbsp;___&nbsp;_",
        "0" => "___&nbsp;___&nbsp;___&nbsp;___&nbsp;___",
        "Ä" => "_&nbsp;___&nbsp;_&nbsp;___",
        "Ö" => "___&nbsp;___&nbsp;___&nbsp;_",
        "Ü" => "_&nbsp;_&nbsp;___&nbsp;___"
    ];
}

function morse_encoder($word) {
    return str_replace(array_keys(get_morse()), get_morse(), strtoupper($word));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="B_KEYER (V7.0) - Convert Morse code to negative keying" />
    <link rel="stylesheet" type="text/css" href="/css/bkeyer7.css" />
    <title>B_KEYER (V7.0)</title>
</head>
<body style="background-color:#fff">

<table cellpadding="3" cellspacing="1" width="100%" class="defaultTable">
    <tr class="header">
        <td colspan="2"></td>
    </tr>
    <tr>
        <td class="defaultTr">
            Convert Morse code to negative equivalent
            <a href="#modal" id="help"><img src="/image/Help.png" alt="Help"></a>
        </td>
    </tr>
</table>
<form method="post" name="Morse_Code" id="Morse_Code">
    <table cellpadding="3" cellspacing="1" class="defaultTable">
        <tr>
            <td class="defaultTr">
                <label for="MorseCode">Please enter NDB IDENT</label>
            </td>
            <td class="catOne">
                <input id="myPitch" name="myPitch" type="hidden" value="<?php echo $myPitch; ?>">
                <input id="mySpeed" name="mySpeed" type="hidden" value="<?php echo $mySpeed; ?>">
                <input type="Text" id="MorseCode" name="MorseCode" size="1" autofocus>
                <button type="submit" id="btn_convert" tabindex="1">Convert</button>
                <button type="button" id="btn_clear" tabindex="1">Clear</button>
            </td>
        </tr>
<?php
    if (isset($m_caption) and isset($m_code) and isset($m_negativ)) {
        echo <<< EOD
    <tr><td class="defaultTr" colspan="2">&nbsp;{$m_caption}</td></tr>
    <tr><td class="defaultTrII" colspan="2"><div style="width:95%; overflow:hidden;">{$m_code}</div></td></tr>
    <tr><td class="defaultTrIII" colspan="2"><div style="width:95%; overflow:hidden;">{$m_negativ}</div></td></tr>
    <tr>
        <td class="defaultTr"></td>
        <td>
            <div id="myDIV1">
                Play positive <button onclick="return PlayMorse('{$m_pos_play}');"><img src="/image/play.png" alt="Play+"></button><br><br>
                Play negative <button onclick="return PlayMorse('{$m_neg_play}');"><img src="/image/play.png" alt="Play-"></button><br><br>
                Pitch [<input type="text" id="myFR" value="{$myPitch}" size="3" style="border-style:none;background: transparent">&nbsp;Hz]:
                <input id="frequency" type="range" min="{$pitchMin}" max="{$pitchMax}" step="10" value="{$myPitch}" onchange="updateText(this.value, myFR, myFR2, frequency2, myPitch);" ><br><br>
                Speed [<input type="text" id="mySP" value="{$mySpeed}" size="3" style="border-style:none;background: transparent">WPM]:
                <input id="speed" type="range" min="{$speedMin}" max="{$speedMax}" step="1" value="{$mySpeed}" onchange="updateText(this.value, mySP, mySP2, speed2, mySpeed);">
            </div>
            <div id="myDIV2">
                Play positive <button disabled="disabled"><img src="/image/no-play.png" alt="Play+" style="width:30px;height:30px;"></button><br><br>
                Play negative <button disabled="disabled"><img src="/image/no-play.png" alt="Play-" style="width:30px;height:30px;"></button><br><br>
                Pitch [<input type="text" disabled="disabled" id="myFR2" value="{$myPitch}" size="3" style="border-style:none;background: transparent">&nbsp;Hz]:
                <input id="frequency2" type="range" min="{$pitchMin}" max="{$pitchMax}" step="10" value="{$myPitch}" disabled="disabled"><br><br>
                Speed [<input type="text" disabled="disabled" id="mySP2" value="{$mySpeed}" size="3" style="border-style:none;background: transparent">WPM]:
                <input id="speed2" type="range" min="{$speedMin}" max="{$speedMax}" step="1" value="{$mySpeed}" disabled="disabled">
            </div>
        </td>
    </tr>
EOD;
    } ?>
    <tr class="header">
        <td colspan="2"></td>
    </tr>
</table>
</form>
<section id="modal" class="flex-center">
    <div class="shade"></div>
    <div class="inner">
        <div class="header">
            <h2>NEGATIVE KEYING PROGRAM, B_KEYER (V7.0)</h2>
            <a href="#" class="close">[Close]</a>
        </div>
        <div class="content">
            <p>This program converts a sequence of Morse characters into their equivalent reverse or 'negative' form. </p>
            <p>Non-Directional Beacons (NDBs) are sometimes received in their 'negative' mode due to a transmitter fault.</p>
            <p>When you would normally hear a tone you hear silence and vice versa - everything else follows from that.</p>
            <p>The program displays the entered call characters in Morse characters, and on the next line  it lines up the equivalent in negative form.</p>
            <p>('X' becomes 'S')</p>
            <p>&nbsp;</p>
            <p>To use B_KEYER:</p>
            <p>&nbsp;</p>
            <p>Enter the heard call character(s) in upper or lower case. e.g. 'CA'.</p>
            <p>If you heard the normal ID plus an additional 'e', this indicates the transmitter is in emergency mode. You can add '(e)' to represent this behaviour, e.g. 'CA(e)'.</p>
            <p>If you heard an extra-long dash (a 'tail' or DAID) between call repeats,  add a '=' as a suffix to represent the dash, e.g. 'CA='.</p>
            <p>If you hear a signal which could be made up from two or more normal morse codes, add a '+' between two characters,</p>
            <p>e.g. 'C+A' for '___ _ ___ _ _ ___'.</p>
            <p>&nbsp;</p>
            <p>The program shows</p>
            <p>&nbsp;</p>
            <p>'&nbsp;&nbsp;&nbsp;&nbsp;?&nbsp;&nbsp;&nbsp;&nbsp;'</p>
            <p>'____?____'</p>
            <p>&nbsp;</p>
            <p>to represent the silence or the DAID between repeats of the call. The '?' is because the gap length varies a lot from NDB to NDB.</p>
            <p>&nbsp;</p>
            <p> You can also use B-KEYER backwards - to input an 'as heard' negative and find the equivalent positive.</p>
            <p>This is a bit more difficult.</p>
            <p></p>
            <p>Listen VERY CAREFULLY to be sure you have heard the suspect neg. accurately.</p>
            <p>When selecting the character(s) to enter, remember '=' means a DAID (if any).</p>
            <p>Try it now with 'FA='  It gives  'D' or 'S', then 'R', then 'T' or 'E' i.e. 'DRT' or 'DRE' or 'SRT' or 'SRE' are the possible positives.</p>
            <p>Try entering each to B-KEYER as positives.   You'll see that the negatives of all four could sound like 'FA' with a long dash.</p>
            <p>&nbsp;</p>
            <p>Finally check your records for an NDB around that freq. with a matching call. If you find one, you have probably succeeded!</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>(C) 2003 by Brian Keyte all rights reserved, including the right to future derivative works.
            <p>This program is freeware. It may be copied freely but not modified by the user.</p>
            <p>The original code has been ported to PHP/HTML/CSS by J. Rabe in June 2020</p>
            <p>&nbsp;</p>
            <p>We'll be pleased to have any comments and suggestions to improve the program.</p>
            <p>( email: bkeyte(at)uwclub.net )</p>
            <p>( email: rabej_de(at)yahoo.de )</p>
        </div>
    </div>
</section>
<script type="application/javascript">
function PlayMorse(inp)
{
    if (!window.AudioContext || window.webkitAudioContext) {
        alert('Audio tone generation is not supported in this browser')
        return false;
    }
    var ctx = new (window.AudioContext || window.webkitAudioContext)();
    var d1 = document.getElementById("myDIV1");
    var d2 = document.getElementById("myDIV2");
    var t = ctx.currentTime;
    var oscillator = ctx.createOscillator();
    var myFreq = document.getElementById("frequency").value;
    var myRate = document.getElementById("speed").value * 1.3;
    var dot = 1.2 / myRate;
    var myDots = 	inp.split('.').length - 1;
    var myDashes = 	inp.split('-').length - 1;
    var myBlanks = 	inp.split(' ').length - 1;
    var myHashes = 	inp.split('#').length - 1;
    var myPipes = 	inp.split('|').length - 1;
    var myDollars = inp.split('$').length - 1;
    var myAmps = 	inp.split('&').length - 1;

    var myTime = ((myDots * dot) + (myDashes * dot * 3) + (myBlanks * dot * 4) + (myHashes * dot * 24) + (myPipes * dot * 34) + (myAmps * dot * 19) + (myDollars * dot * 14)) * 1000;

    d1.style.display = "none";
    d2.style.display = "block";
    oscillator.type = "sine";
    oscillator.frequency.value = myFreq;

    var gainNode = ctx.createGain();
    gainNode.gain.setTargetAtTime(0, t, 0.004);

    inp.split("").forEach(
        function(letter) {
            switch(letter) {
                case ".":
                    gainNode.gain.setTargetAtTime(1, t, 0.004);
                    t += dot;
                    gainNode.gain.setTargetAtTime(0, t, 0.004);
                    t += dot;
                    break;
                case "-":
                    gainNode.gain.setTargetAtTime(1, t, 0.004);
                    t += 3 * dot;
                    gainNode.gain.setTargetAtTime(0, t, 0.004);
                    t += dot;
                    break;
                case " ":
                    t += 4 * dot;
                    break;
                case "#":
                    t += 28 * dot;
                    break;
                case "$":
                    t += 14 * dot;
                    break;
                case "|":
                    gainNode.gain.setTargetAtTime(1, t, 0.004);
                    t += 28 * dot;
                    gainNode.gain.setTargetAtTime(0, t, 0.004);
                    t += dot;
                    break;
                case "&":
                    gainNode.gain.setTargetAtTime(1, t, 0.004);
                    t += 14 * dot;
                    gainNode.gain.setTargetAtTime(0, t, 0.004);
                    t += dot;
                    break;							}
        }
    );
    oscillator.connect(gainNode);
    gainNode.connect(ctx.destination);
    oscillator.start();

    setTimeout(function() {
        d1.style.display = "block";
        d2.style.display = "none";
    }, myTime)

    return false;
}

function updateText(val, F1, F2, F3, F4) {
    F1.value = val;
    F2.value = val;
    F3.value = val;
    F4.value = val;
}
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('btn_clear').onclick = function(){
        document.getElementById('MorseCode').value = '';
        document.getElementById('btn_convert').click();
    }
});
</script>
</body>
</html>