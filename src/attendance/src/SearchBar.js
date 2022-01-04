import React from 'react';

export default SearchBar

function SearchBar(props) {
  const searchTerm = props.searchTerm;
  const setSearchTerm = props.setSearchTerm;

  const filterRecent = props.filterRecent;
  const setFilterRecent = props.setFilterRecent;
  const filterOld = props.filterOld;
  const setFilterOld = props.setFilterOld;
  const filterNew = props.filterNew;
  const setFilterNew = props.setFilterNew;
  const filterPresent = props.filterPresent;
  const setFilterPresent = props.setFilterPresent;

  const searchElement = searchTerm ?
    (<input type="search" name="search" placeholder="Search" value={searchTerm}
      onChange={(event) => setSearchTerm(event.target.value)} />)
    : (<input type="search" name="search" results="0" placeholder="Search" value={searchTerm}
      onChange={(event) => setSearchTerm(event.target.value)} />);

  return (
    <div className='SearchBar'>
      <form>
        {searchElement}
        <br />
        <input type="checkbox" name="recent" id="recent" checked={!!filterRecent}
          onChange={(event) => setFilterRecent(event.target.checked)} />
        <label htmlFor="recent">recent</label>
        <input type="checkbox" name="old" id="old" checked={!!filterOld}
          onChange={(event) => setFilterOld(event.target.checked)} />
        <label htmlFor="old">rusty</label>
        <input type="checkbox" name="new" id="new" checked={!!filterNew}
          onChange={(event) => setFilterNew(event.target.checked)} />
        <label htmlFor="new">new folks</label>
        <input type="checkbox" name="attendees" id="attendees" checked={!!filterPresent}
          onChange={(event) => setFilterPresent(event.target.checked)} />
        <label htmlFor="attendees">attendees</label>
      </form>
    </div>
  )
}
