<?php

use overplex\wajaxcrud\rangecolumn\RangeColumnWidget;
use wodrow\yii2wtools\tools\JsBlock;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var RangeColumnWidget $widget
 */

$wid = "RangeColumn{$widget->id}";

$model = $widget->model;
$attribute = $widget->attribute;
$value = $model->$attribute;
if (!is_null($value) && strpos($value, ' - ') !== false) {
    list($s, $e) = explode(' - ', $value);
} else {
    $s = $e = "";
}
?>

    <div id="<?= $wid ?>">
        <?= Html::activeInput('hidden', $model, $attribute, [
            'class' => "form-control range-v",
            'placeholder' => Yii::t('app', 'Interval'),
            '_id' => "range-v-" . $wid,
            'data-minV' => $s,
            'data-maxV' => $e
        ]); ?>
        <div class="input-group">
            <span class="input-group-addon" _name="min-v" contenteditable="true"><?= $s ?></span>
            <span class="input-group-addon">~</span>
            <span class="input-group-addon" _name="max-v" contenteditable="true"><?= $e ?></span>
            <span class="input-group-btn">
            <button class="btn btn-primary" type="button"
                    _id="ranger-filter-<?= $wid ?>"><?= Yii::t('app', 'determine') ?></button>
        </span>
        </div>
    </div>

<?php JsBlock::begin(); ?>
    <script>
        $(function () {

            function rangeColumnIsNumber(val) {
                let regPos = /^\d+(\.\d+)?$/; // Non-negative floating point
                let regNeg = /^(-(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*)))$/; // Floating point number
                return regPos.test(val) || regNeg.test(val);
            }

            $(document).on('input', 'span[_name=\'min-v\']', function (e) {
                let minV = e.target.innerHTML;
                $("#<?=$wid ?>").find("input[_id='range-v-<?=$wid ?>']").attr('data-minV', minV);
            });

            $(document).on('input', 'span[_name=\'max-v\']', function (e) {
                let maxV = e.target.innerHTML;
                $("#<?=$wid ?>").find("input[_id='range-v-<?=$wid ?>']").attr('data-maxV', maxV);
            });

            $(document).on('click', "button[_id='ranger-filter-<?=$wid ?>']", function () {

                let minV = $(this).parents("#<?=$wid ?>").find("input[_id='range-v-<?=$wid ?>']").attr('data-minV');
                let maxV = $(this).parents("#<?=$wid ?>").find("input[_id='range-v-<?=$wid ?>']").attr('data-maxV');

                if (minV) {
                    if (!rangeColumnIsNumber(minV)) {
                        alert(yii.t('app', 'The minimum value must be a number'));
                        return;
                    }
                }

                if (maxV) {
                    if (!rangeColumnIsNumber(maxV)) {
                        alert(yii.t('app', 'Maximum value must be a number'));
                        return;
                    }
                }

                if (minV && maxV) {
                    if (maxV < minV) {
                        alert(yii.t('app', 'The maximum value must be greater than the minimum value'));
                        return;
                    }
                }

                let rangeV = '';

                if (minV && !maxV) {
                    rangeV = '';
                }

                if (minV && !maxV) {
                    rangeV = minV + ' - ';
                }

                if (!minV && maxV) {
                    rangeV = ' - ' + maxV;
                }

                if (minV && maxV) {
                    rangeV = minV + ' - ' + maxV;
                }

                let e13 = $.Event('keydown');
                e13.keyCode = 13;
                $("#<?=$wid ?>").find("input[_id='range-v-<?=$wid ?>']").val(rangeV).trigger(e13);

            });
        });
    </script>
<?php JsBlock::end(); ?>