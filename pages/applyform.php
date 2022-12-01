<?php
require_once("../api/lib/params.php");
?>
<div class="row">
  <div class="col-lg-1" align="center"></div>
  <div class="col-lg-10" align="center">
    <div class="col-lg-12" align="center" id="checkdata"> </div>
    <div class="input-group  has-error">
        <span class="input-group-addon" id="basic-lblname">姓　　名 ： </span>
        <input type="text" class="form-control" id="basic-name">
    </div>
    <div class="input-group  has-error">
        <span class="input-group-addon" id="basic-lbltel">電　　話 ： </span>
        <input type="text" class="form-control" id="basic-tel" placeholder="範例 : 0911222333 或 079876543">
        <span class="input-group-addon" id="basic-lblsex">性　　別 ： </span>
        <select class="form-control" id="basic-sex">
          <option value='0'>-</option>
          <option value='M'>男</option>
          <option value='F'>女</option>
        </select>
        <span class="input-group-addon" id="basic-lblarea">區　　別 ： </span>
        <select  class="form-control" id="basic-area">
            <option value='0'>-</option>
            <option value='A'>北區</option>
            <option value='B'>中區</option>
            <option value='C'>雲嘉</option>
            <option value='D'>園區</option>
            <option value='E'>南區</option>
            <option value='F'>高區</option>
            <option value='G'>海外</option>
        </select>
    </div>

    <!--<hr>-->
    <hr>
    <div class="input-group  has-error">
        <span class="input-group-addon" id="basic-lblgroup">義工組別 ： </span>
        <select style="width:60%" class="form-control" id="basic-group">
            <option value=0>-</option>
            <?php
              for($i = 0; $i < count($params_maingroups); $i++) {
                echo '<option value='.($i+1).'>'.$params_maingroups[$i].'</option>';
              }
            ?>
        </select>
        <select style="width:40% " class="form-control" id="basic-subgroup">
            <option value=0>-</option>
        </select>
        <span class="input-group-addon" id="basic-lblspecialcase">義工類別 ： </span>
        <select class="form-control" id="basic-type">
          <option value='0'>-</option>
          <?php
            for($i = 0; $i < count($params_volunteer); $i++) {
              echo '<option value='.($i+1).'>'.$params_volunteer[$i].'</option>';
            }
          ?>
        </select>
    </div>
    <div class="input-group  has-error">
      <?php
        echo '<span class="input-group-addon" id="basic-lblgroup">參加日期 ： </span>';
        echo '<div class="input-group-btn" style="width:24%">';
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d1" value="'.$praydays[0].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join1" '.($praydays[0] ? 'checked>' : 'unchecked disabled>');
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d5" value="'.$praydays[4].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join5" '.($praydays[4] ? 'checked>' : 'unchecked disabled>');
        echo '</div>';
        echo '<div class="input-group-btn" style="width:24%;">';
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d2" value="'.$praydays[1].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join2" '.($praydays[1] ? 'checked>' : 'unchecked disabled>');
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d6" value="'.$praydays[5].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join6" '.($praydays[5] ? 'checked>' : 'unchecked disabled>');
        echo '</div>';
        echo '<div class="input-group-btn" style="width:24%">';
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d3" value="'.$praydays[2].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join3" '.($praydays[2] ? 'checked>' : 'unchecked disabled>');
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d7" value="'.$praydays[6].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join7" '.($praydays[6] ? 'checked>' : 'unchecked disabled>');
        echo '</div>';
        echo '<div class="input-group-btn" style="width:24%">';
        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d4" value="'.$praydays[3].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join4" '.($praydays[3] ? 'checked>' : 'unchecked disabled>');

        echo '    <input type="text" style="text-align: center;" class="form-control" id="basic-d8" value="'.$praydays[7].'" readonly>';
        echo '    <input type="checkbox" class="form-control sx-checkbox" id="basic-join8" '.($praydays[7] ? 'checked>' : 'unchecked disabled>');
        echo '</div>';
      ?>
    </div>
    <div class="input-group  has-success">
        <span class="input-group-addon" id="basic-lblmemo">備　　註 ： </span>
        <input type="text" class="form-control" id="basic-memo">
    </div>
    <div class="col-lg-1" align="center"></div>
</div>
