<?php
    require_once("../api/lib/params.php");
    date_default_timezone_set('Asia/Taipei');
    $currDate = date('Y-m-d');
    $apply = $_SESSION["account"];

    for($i = 0; $i < count($params_maingroups); $i++) {
      $value = join(";", $params_subgroups[$params_maingroups[$i]]);
      echo '<input type="hidden" id="subgroup'.($i+1).'" class="subgroup" name="subgroup'.($i+1).'" value="'.($value).';">';
    }

    echo "<input type='hidden' id='basic-date' class='basic-date' name='basic-date' value='".$currDate."' />";
    echo "<input type='hidden' id='basic-apply' class='basic-apply' name='basic-apply' value='".$apply."' />";
    echo "<input type='hidden' id='basic-id' class='basic-id' name='basic-id' value=0 />";
    echo "<input type='hidden' id='basic-serial' class='basic-serial' name='basic-serial' value=0 />";
    echo "<input type='hidden' id='basic-deleteid' class='basic-deleteid' name='basic-deleteid' value=0 />";
    echo "<input type='hidden' id='basic-deleteserial' class='basic-deleteserial' name='basic-deleteserial' value=0 />";

    $value = join(";", $params_volunteer);
    echo '<input type="hidden" id="typeitem" class="typeitem" name="typeitem" value="'.($value).';">';

    $value = join(";", $params_clsother);
    echo '<input type="hidden" id="clsother" class="clsother" name="clsother" value="'.($value).';">';

