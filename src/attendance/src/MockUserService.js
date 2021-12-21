/// Mock webservice for getting users from wordpress.
/// See: https://dzone.com/articles/consuming-rest-api-with-reactjs
/// See: https://developer.wordpress.org/rest-api/reference/users/

class MockUserService {

    constructor() {
        this.users = [{
            id: 99,
            name: 'Zaphod',
            first_name: 'Zaphond',
            last_name: 'Beeblebrox',
            email: 'zaphod@example.com',
            description: 'Presedent of the Galaxy',
            nickname: 'Zaphod Dude'
        },
        {
            id: 1099,
            name: 'FordP',
            first_name: 'Ford',
            last_name: 'Prefect',
            email: 'ford@example.com',
            description: 'Alien',
            nickname: 'Fnord'
        },
        {
            id: 1100,
            name: 'AurthorD',
            first_name: 'Arthor',
            last_name: 'Dent',
            email: 'arthor@example.com',
            description: 'Sad Earthline',
            nickname: 'DentArthorDent'
        },
        {
            id: 1101,
            name: 'MarvinA',
            first_name: 'Marvin',
            last_name: 'Android',
            email: 'marvin@example.com',
            description: 'Marvin the paranoid android',
            nickname: 'SadMarvin'
        },
        { id: 100, first_name: 'Alice', last_name: 'Atalan', description: 'A Tuba' },
        { id: 101, first_name: 'Bob', last_name: 'Shamilov', description: 'Bob is a fella' },
        { id: 102, first_name: 'Charlie', last_name: 'Suloev', description: 'C Saxophone' },
        { id: 103, first_name: 'Denise', last_name: 'Khalil' },
        { id: 104, first_name: 'Ethan', last_name: 'Usoyan', },

        { id: 2104, first_name: 'Alexis', last_name: 'Adi', description: 'Trumpet'},
        { id: 2105, first_name: 'Aline', last_name: 'Shavershian', description: 'Percussion'},
        { id: 2106, first_name: 'Arturo', last_name: 'Mori', description: 'Percussion, Sax, Video, Photo.'},
        { id: 2107, first_name: 'Alma', last_name: 'Tamoyan', description: 'We are music because we all are energy flowing.'},
        { id: 2108, first_name: 'Andrew', last_name: 'Ilyas', description: 'Talented, industrious, unsuccessful musician.'},
        { id: 2109, first_name: 'Brandon', last_name: 'Khario', description: 'Cornet'},
        { id: 2110, first_name: 'Brian', last_name: 'Serdar', description: 'Everything I Can'},
        { id: 2111, first_name: 'Clair', last_name: 'Evdal', description: 'Sousaphone, Trombone'},
        { id: 2112, first_name: 'Fred', last_name: 'Boyik', description: 'Multi-instrumentalist. I have played with The Carnival Band for over 10 years. Previously played with numerous Vancouver Bands.'},
        { id: 2113, first_name: 'Hayley', last_name: 'Mirza', description: 'Percussion.'},
        { id: 2114, first_name: 'Helen', last_name: 'Khalif', description: 'Knitter Potter Tuber.'},
        { id: 2115, first_name: 'Henry', last_name: 'Adzhoyev', description: 'Dancer'},
        { id: 2116, first_name: 'James', last_name: 'Shero', description: 'It is not the size that counts, it is how you blow it.'},
        { id: 2117, first_name: 'Max', last_name: 'Hajoyan', description: 'Trombone'}

        ];
    }

    async retrieve(page, per_page) {
        if (page === 1) {
            await new Promise((res) => setTimeout(res, 1000 * Math.random()));
            return Promise.resolve(this.users);       
         }
         return Promise.resolve([]); 
    }
}

export default MockUserService;
