# 2.0.2
- Element names can include numbers now

# 2.0.1
- Colons are now allowed in element data names
# Upgrading from 1.x to 2.0.0
Previously, if you passed in your own instance of `XMLWriter` into an instance of `XMLWriterService`,
`XMLWriter::openMemory` was called from within the `XMLWriterService`. This did not allow for customizations made to the XMLWriter object prior to it being passed into `XMLWriterService`

In 2.0, you are required to call `XMLWriter::openMemory` prior to passing an instance into `XMLWriterService`.
