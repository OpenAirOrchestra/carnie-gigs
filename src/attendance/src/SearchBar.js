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

  return (
    <div className='SearchBar'>
      <form>
        <input type="text" name="search" placeholder="&#128269;" value={searchTerm}
          onChange={(event) => setSearchTerm(event.target.value)} />
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
