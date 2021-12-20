import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';

import AttendanceSheet from './AttendanceSheet'


function App() {

  return (
    <div className="App">
      <AttendanceSheet />
    </div>
  );
}

// ========================================


ReactDOM.render(
  <App />,
  document.getElementById('root')
);
