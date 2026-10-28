import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Switch from 'flarum/common/components/Switch';
import Button from 'flarum/common/components/Button';

class FestiveDecorationsPage extends ExtensionPage {
  content() {
    return (
      <div className="ExtensionPage-settings">
        <div className="container">
          <form onsubmit={this.onsubmit.bind(this)}>
            <h3>Generale</h3>
            <div className="Form-group">
              {Switch.component({
                state: this.setting('peopleinside-festive-decorations.enabled')() === '1',
                onchange: (val) => this.setting('peopleinside-festive-decorations.enabled')(val ? '1' : '0'),
                label: 'Attiva decorazioni festive',
                help: 'Interruttore generale per tutte le decorazioni e gli effetti.',
              })}
            </div>

            <h3>Festività e Stagioni</h3>
            <div className="Form-group">
              <p>Seleziona le festività e le stagioni da attivare automaticamente:</p>
              {this.buildHolidayCheckboxes()}
            </div>

            <h3>Effetto Neve ❄️</h3>
            <div className="Form-group">
              {Switch.component({
                state: this.setting('peopleinside-festive-decorations.snow_enabled')() === '1',
                onchange: (val) => this.setting('peopleinside-festive-decorations.snow_enabled')(val ? '1' : '0'),
                label: 'Attiva effetto neve',
              })}
              {Switch.component({
                state: this.setting('peopleinside-festive-decorations.snow_always')() === '1',
                onchange: (val) => this.setting('peopleinside-festive-decorations.snow_always')(val ? '1' : '0'),
                label: 'Forza neve tutto l\'anno (ignora le date)',
              })}
              <div className="helpText">Periodo di attivazione automatica:</div>
              <div style={{display: 'flex', gap: '10px', marginTop: '10px', alignItems: 'center'}}>
                <label>Dal:</label>
                <input 
                  type="text" 
                  className="FormControl" 
                  bidi={this.setting('peopleinside-festive-decorations.snow_start')} 
                  placeholder="GG/MM" 
                  style={{width: '80px', textAlign: 'center'}} 
                />
                <label>Al:</label>
                <input 
                  type="text" 
                  className="FormControl" 
                  bidi={this.setting('peopleinside-festive-decorations.snow_end')} 
                  placeholder="GG/MM" 
                  style={{width: '80px', textAlign: 'center'}} 
                />
              </div>
            </div>

            <h3>Effetto Lucciole ✨</h3>
            <div className="Form-group">
              {Switch.component({
                state: this.setting('peopleinside-festive-decorations.fireflies_enabled')() === '1',
                onchange: (val) => this.setting('peopleinside-festive-decorations.fireflies_enabled')(val ? '1' : '0'),
                label: 'Attiva effetto lucciole',
              })}
              {Switch.component({
                state: this.setting('peopleinside-festive-decorations.ff_always')() === '1',
                onchange: (val) => this.setting('peopleinside-festive-decorations.ff_always')(val ? '1' : '0'),
                label: 'Forza lucciole tutto l\'anno (ignora le date)',
              })}
              <div className="helpText">Periodo di attivazione automatica:</div>
              <div style={{display: 'flex', gap: '10px', marginTop: '10px', alignItems: 'center'}}>
                <label>Dal:</label>
                <input 
                  type="text" 
                  className="FormControl" 
                  bidi={this.setting('peopleinside-festive-decorations.ff_start')} 
                  placeholder="GG/MM" 
                  style={{width: '80px', textAlign: 'center'}} 
                />
                <label>Al:</label>
                <input 
                  type="text" 
                  className="FormControl" 
                  bidi={this.setting('peopleinside-festive-decorations.ff_end')} 
                  placeholder="GG/MM" 
                  style={{width: '80px', textAlign: 'center'}} 
                />
              </div>
            </div>

            <div className="Form-group">
              {Button.component({
                className: 'Button Button--primary',
                type: 'submit',
                loading: this.loading,
                children: 'Salva modifiche',
              })}
            </div>
          </form>
        </div>
      </div>
    );
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

    const currentHolidays = this.setting('peopleinside-festive-decorations.holidays')();
    const holidaysArray = currentHolidays ? JSON.parse(currentHolidays) : ['natale', 'capodanno', 'halloween'];

    return holidays.map(h => {
      const isChecked = holidaysArray.includes(h.key);
      return (
        <label key={h.key} style={{ display: 'block', marginBottom: '5px', cursor: 'pointer' }}>
          <input 
            type="checkbox" 
            checked={isChecked} 
            onchange={(e) => {
              const current = [...holidaysArray];
              if (e.target.checked) {
                current.push(h.key);
              } else {
                const idx = current.indexOf(h.key);
                if (idx > -1) current.splice(idx, 1);
              }
              this.setting('peopleinside-festive-decorations.holidays')(JSON.stringify(current));
            }}
          /> {h.label}
        </label>
      );
    });
  }
}

app.initializers.add('peopleinside-festive-decorations', () => {
  app.extensionData
    .for('peopleinside-festive-decorations')
    .registerPage(FestiveDecorationsPage);
});
