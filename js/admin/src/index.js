import app from 'flarum/common/app';
import SettingsPage from 'flarum/admin/components/SettingsPage';
import Button from 'flarum/common/components/Button';
import Switch from 'flarum/common/components/Switch';
import Stream from 'flarum/common/utils/Stream';

app.initializers.add('peopleinside-festive-decorations', () => {
  app.extensionData
    .for('peopleinside-festive-decorations')
    .registerPage({
      component: FestiveDecorationsSettingsPage,
      name: 'peopleinside-festive-decorations.admin.settings_title',
      icon: 'fas fa-snowflake',
    });
});

class FestiveDecorationsSettingsPage extends SettingsPage {
  oninit(vnode) {
    super.oninit(vnode);
    this.enabled = Stream(app.data.settings['peopleinside-festive-decorations.enabled'] || false);
    
    const defaultHolidays = ['natale', 'capodanno', 'halloween'];
    const savedHolidays = app.data.settings['peopleinside-festive-decorations.holidays'];
    this.holidays = Stream(savedHolidays ? JSON.parse(savedHolidays) : defaultHolidays);
    
    this.snowEnabled = Stream(app.data.settings['peopleinside-festive-decorations.snow_enabled'] || false);
    this.snowStart = Stream(app.data.settings['peopleinside-festive-decorations.snow_start'] || '25/10');
    this.snowEnd = Stream(app.data.settings['peopleinside-festive-decorations.snow_end'] || '07/01');
    this.snowAlways = Stream(app.data.settings['peopleinside-festive-decorations.snow_always'] || false);

    this.ffEnabled = Stream(app.data.settings['peopleinside-festive-decorations.fireflies_enabled'] || false);
    this.ffStart = Stream(app.data.settings['peopleinside-festive-decorations.ff_start'] || '08/01');
    this.ffEnd = Stream(app.data.settings['peopleinside-festive-decorations.ff_end'] || '24/10');
    this.ffAlways = Stream(app.data.settings['peopleinside-festive-decorations.ff_always'] || false);
  }

  content() {
    return [
      <div className="Form">
        <h3>{app.translator.trans('peopleinside-festive-decorations.admin.general_title')}</h3>
        <div className="Form-group">
          {Switch.component({
            state: this.enabled(),
            onchange: this.enabled,
            label: app.translator.trans('peopleinside-festive-decorations.admin.enable_label'),
            help: app.translator.trans('peopleinside-festive-decorations.admin.enable_help'),
          })}
        </div>
        
        <h3>{app.translator.trans('peopleinside-festive-decorations.admin.holidays_title')}</h3>
        <div className="Form-group">
          {this.buildHolidayCheckboxes()}
        </div>

        <h3>{app.translator.trans('peopleinside-festive-decorations.admin.snow_title')}</h3>
        <div className="Form-group">
          {Switch.component({
            state: this.snowEnabled(),
            onchange: this.snowEnabled,
            label: app.translator.trans('peopleinside-festive-decorations.admin.snow_enable_label'),
          })}
          {Switch.component({
            state: this.snowAlways(),
            onchange: this.snowAlways,
            label: app.translator.trans('peopleinside-festive-decorations.admin.snow_always_label'),
          })}
          <div className="helpText">{app.translator.trans('peopleinside-festive-decorations.admin.snow_dates_help')}</div>
          <div style={{display: 'flex', gap: '10px', marginTop: '10px', alignItems: 'center'}}>
            <input type="text" className="FormControl" bidi={this.snowStart} placeholder="GG/MM" style={{width: '80px', textAlign: 'center'}} />
            <span>{app.translator.trans('peopleinside-festive-decorations.admin.to')}</span>
            <input type="text" className="FormControl" bidi={this.snowEnd} placeholder="GG/MM" style={{width: '80px', textAlign: 'center'}} />
          </div>
        </div>

        <h3>{app.translator.trans('peopleinside-festive-decorations.admin.fireflies_title')}</h3>
        <div className="Form-group">
          {Switch.component({
            state: this.ffEnabled(),
            onchange: this.ffEnabled,
            label: app.translator.trans('peopleinside-festive-decorations.admin.ff_enable_label'),
          })}
          {Switch.component({
            state: this.ffAlways(),
            onchange: this.ffAlways,
            label: app.translator.trans('peopleinside-festive-decorations.admin.ff_always_label'),
          })}
          <div className="helpText">{app.translator.trans('peopleinside-festive-decorations.admin.ff_dates_help')}</div>
          <div style={{display: 'flex', gap: '10px', marginTop: '10px', alignItems: 'center'}}>
            <input type="text" className="FormControl" bidi={this.ffStart} placeholder="GG/MM" style={{width: '80px', textAlign: 'center'}} />
            <span>{app.translator.trans('peopleinside-festive-decorations.admin.to')}</span>
            <input type="text" className="FormControl" bidi={this.ffEnd} placeholder="GG/MM" style={{width: '80px', textAlign: 'center'}} />
          </div>
        </div>

        <div className="Form-group">
          {Button.component({
            className: 'Button Button--primary',
            type: 'submit',
            loading: this.loading,
            children: app.translator.trans('core.admin.settings.submit_button'),
          })}
        </div>
      </div>
    ];
  }

  buildHolidayCheckboxes() {
    const holidays = [
      { key: 'natale', label: 'Natale 🎄' },
      { key: 'capodanno', label: 'Capodanno 🎆' },
      { key: 'halloween', label: 'Halloween 🎃' },
      { key: 'carnevale', label: 'Carnevale 🎭' },
      { key: 'sanvalentino', label: 'San Valentino 💘' },
      { key: 'festadonna', label: 'Festa della donna 🌼' },
      { key: 'pasqua', label: 'Pasqua 🐣' },
      { key: 'festamamma', label: 'Festa della mamma 💐' },
      { key: 'pride', label: 'Pride 🏳️‍🌈' },
      { key: 'primavera', label: 'Primavera 🌷' },
      { key: 'estate', label: 'Estate ☀️' },
      { key: 'autunno', label: 'Autunno 🍂' },
      { key: 'inverno', label: 'Inverno ❄️' },
    ];

    return holidays.map(h => {
      const isChecked = this.holidays().includes(h.key);
      return (
        <label key={h.key} style={{ display: 'block', marginBottom: '5px', cursor: 'pointer' }}>
          <input 
            type="checkbox" 
            checked={isChecked} 
            onchange={(e) => {
              const current = [...this.holidays()];
              if (e.target.checked) {
                current.push(h.key);
              } else {
                const idx = current.indexOf(h.key);
                if (idx > -1) current.splice(idx, 1);
              }
              this.holidays(current);
            }}
          /> {h.label}
        </label>
      );
    });
  }

  settings() {
    return {
      'peopleinside-festive-decorations.enabled': this.enabled(),
      'peopleinside-festive-decorations.holidays': JSON.stringify(this.holidays()),
      'peopleinside-festive-decorations.snow_enabled': this.snowEnabled(),
      'peopleinside-festive-decorations.snow_start': this.snowStart(),
      'peopleinside-festive-decorations.snow_end': this.snowEnd(),
      'peopleinside-festive-decorations.snow_always': this.snowAlways(),
      'peopleinside-festive-decorations.fireflies_enabled': this.ffEnabled(),
      'peopleinside-festive-decorations.ff_start': this.ffStart(),
      'peopleinside-festive-decorations.ff_end': this.ffEnd(),
      'peopleinside-festive-decorations.ff_always': this.ffAlways(),
    };
  }
}
