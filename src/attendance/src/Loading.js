import React from 'react';

export default Loading

function Loading(props) {
    const isLoading = props.isLoading;

    if (!isLoading) {
        return null;
    }

    return (
        <div className='Loading'>
            <div className='inner'>
                <div className='spinner'></div>
            </div>
        </div>
    );
}