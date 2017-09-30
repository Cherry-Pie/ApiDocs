<div class="form-group">
    <label class="col-sm-1 control-label"><span class="label label-info">{{ $param['type'] }}</span></label>
    <label class="col-sm-2 control-label">{{ $param['name'] }}</label>
    <div class="col-sm-9">
        <input type="number"
        pattern="\\d*"
        class="form-control"
        placeholder="{{ $param['description'] }}"
        name="{{ $param['name'] }}"
        readonly
        onfocus="if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }">
    </div>
</div>
