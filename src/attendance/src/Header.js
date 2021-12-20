import React from 'react';

export default Header

function Header(props) {
  const name = props.name;
  const url = props.url;

  let element =  <h1>Loading...</h1>;

  if (name) {
    element = <h1>>Attendance for {name}</h1>;
  }
  if (url && name) {
    element =  <h1>Attendance for <a href={url}>{name}</a></h1>;
  } 
  
  return (
    <div className='Header'>
      {element}
    </div>
  )
}
