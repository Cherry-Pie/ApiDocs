<div class="form-group">
    <label class="col-sm-1 control-label"><span class="label label-info">{{ $param['type'] }}</span></label>
    <label class="col-sm-2 control-label">{{ $param['name'] }}</label>
    <div class="col-sm-9">
        
        <select class="form-control select" 
                name="{{ $param['name'] }}"
                readonly
                onfocus="if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }">
            <option selected disabled value="">{{ $param['description'] }}</option>
            <option value="true">true</option>
            <option value="false">false</option>
        </select>
    </div>
</div>
