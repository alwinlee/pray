<?php
    date_default_timezone_set('Asia/Taipei');
    $currDate = date('Y-m-d');
    $apply = $_SESSION["account"];

    echo '<input type="hidden" id="subgroup1" class="subgroup" name="subgroup1" value="大組;粉專組;聽抄組;">';
    echo '<input type="hidden" id="subgroup2" class="subgroup" name="subgroup2" value="大組;報到組;生服組;服務引導組;保健組;">';
    echo '<input type="hidden" id="subgroup3" class="subgroup" name="subgroup3" value="大組;場地組;監修組;環保組;會計組;資材組;">';
    echo '<input type="hidden" id="subgroup4" class="subgroup" name="subgroup4" value="大組;公關組;感恩餐會;">';
    echo '<input type="hidden" id="subgroup5" class="subgroup" name="subgroup5" value="大組;廣供/壇城組;十供養組;水月觀音組;珠寶組;牌位;">';
    echo '<input type="hidden" id="subgroup6" class="subgroup" name="subgroup6" value="大組;餐食組;茶水組;">';
    echo '<input type="hidden" id="subgroup7" class="subgroup" name="subgroup7" value="大組;機動組;接駁組;道路組;車場組;">';
    echo '<input type="hidden" id="subgroup8" class="subgroup" name="subgroup8" value="大組;音響組;影像組;導播組;播放組;系統組;設備組;網傳組;空拍組;影製組;平拍組;美工組;資料組;宣傳組;行政組;培訓組;">';
    echo '<input type="hidden" id="subgroup9" class="subgroup" name="subgroup9" value="大組;" >';
    echo '<input type="hidden" id="subgroup10" class="subgroup" name="subgroup10" value="大組;" >';
    echo '<input type="hidden" id="subgroup11" class="subgroup" name="subgroup11" value="大組;" >';
    echo '<input type="hidden" id="subgroup12" class="subgroup" name="subgroup12" value="大組;" >';
    echo '<input type="hidden" id="subgroup13" class="subgroup" name="subgroup13" value="大組;" >';
    echo '<input type="hidden" id="subgroup14" class="subgroup" name="subgroup14" value="大組;" >';
    echo '<input type="hidden" id="subgroup15" class="subgroup" name="subgroup15" value="大組;" >';
    echo '<input type="hidden" id="subgroup16" class="subgroup" name="subgroup16" value="大組;" >';
    echo '<input type="hidden" id="subgroup17" class="subgroup" name="subgroup17" value="大組;" >';
    echo '<input type="hidden" id="subgroup18" class="subgroup" name="subgroup18" value="大組;" >';
    echo '<input type="hidden" id="subgroup19" class="subgroup" name="subgroup19" value="大組;" >';
    echo '<input type="hidden" id="subgroup20" class="subgroup" name="subgroup20" value="大會;" >';

    echo "<input type='hidden' id='basic-date' class='basic-date' name='basic-date' value='".$currDate."' />";
    echo "<input type='hidden' id='basic-apply' class='basic-apply' name='basic-apply' value='".$apply."' />";
    echo "<input type='hidden' id='basic-id' class='basic-id' name='basic-id' value=0 />";
    echo "<input type='hidden' id='basic-serial' class='basic-serial' name='basic-serial' value=0 />";
    echo "<input type='hidden' id='basic-deleteid' class='basic-deleteid' name='basic-deleteid' value=0 />";
    echo "<input type='hidden' id='basic-deleteserial' class='basic-deleteserial' name='basic-deleteserial' value=0 />";
    echo "<input type='hidden' id='typeitem' class='typeitem' name='typeitem' value='總護持;副總護持;大會助理;顧問;大組長;副大組長;大組助理;小組長;副小組長;義工;見習幹部;見習助理;助理;' />";
    echo "<input type='hidden' id='clsother' class='clsother' name='clsother' value='廠商;廠商義工;' />";
