const fs = require('fs');
const archiver = require('archiver');
const path = require('path');

// create a file to stream archive data to.
const output = fs.createWriteStream(path.join(__dirname, '..', 'extensions-keep.zip'));
const archive = archiver('zip', {
  zlib: { level: 9 } // Sets the compression level.
});

// pipe archive data to the file
archive.pipe(output);

// append files
archive.file('extensions-keep.php', { name: 'extensions-keep.php' });
archive.file('readme.txt', { name: 'readme.txt' });

// append empty directory
archive.append(null, { name: 'languages/' });

// append files from a sub-directory
archive.directory('includes/', 'includes');
archive.directory('admin/', 'admin');
archive.directory('assets/', 'assets');

// finalize the archive
archive.finalize();