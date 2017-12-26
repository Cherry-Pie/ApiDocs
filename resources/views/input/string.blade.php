<div class="form-group">
    <label class="col-sm-1 control-label"><span class="label label-info">{{ $param['type'] }}</span></label>
    <div class="col-sm-2 control-label" style="text-align: left;overflow: hidden;height: 29px;padding-bottom: 2px;">
        <a href="javascript:void(0)" class="btn btn-default btn-xs" style="position: absolute;right: 0px;bottom: 0;height: 1px;width: 100%;" onclick="if ($(this).hasClass('expanded')) { $(this).parent().css('height', '29px'); $(this).removeClass('expanded'); } else { $(this).parent().css('height', '100%'); $(this).addClass('expanded');}">
            <span class="cr"></span>
        </a>
        @foreach ($param['rules'] as $rule)
            <span class="label label-warning">{{ $rule }}</span>
        @endforeach
    </div>
    <label class="col-sm-2 control-label">{{ $param['name'] }}</label>
    <div class="col-sm-7">
        <input type="text"
        class="form-control"
        placeholder="{{ $param['description'] }}"
        name="{{ $param['name'] }}"
        readonly
        onfocus="if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }">
    </div>
</div>
