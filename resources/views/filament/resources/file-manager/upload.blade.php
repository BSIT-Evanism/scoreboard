<x-filament::page>
    <div
        x-data="{
            files: [],
            isDragging: false,
            draggedFilesCount: 0,
            
            handleDragOver(e) {
                e.preventDefault();
                this.isDragging = true;
                
                const items = e.dataTransfer.items;
                const validFiles = Array.from(items).filter(item => {
                    if (item.kind === 'file') {
                        const type = item.type.toLowerCase();
                        return type.startsWith('image/') || 
                               type === 'application/pdf' ||
                               type === 'application/msword' ||
                               type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                               type === 'application/vnd.ms-excel' ||
                               type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                               type === 'text/csv';
                    }
                    return false;
                });
                
                this.draggedFilesCount = validFiles.length;
            },
            
            handleDragLeave(e) {
                e.preventDefault();
                this.isDragging = false;
                this.draggedFilesCount = 0;
            },
            
            handleDrop(e) {
                e.preventDefault();
                this.isDragging = false;
                
                const droppedFiles = Array.from(e.dataTransfer.files);
                this.addFiles(droppedFiles);
            },
            
            addFiles(newFiles) {
                const filesToAdd = Array.from(newFiles).map(file => ({
                    id: Math.random().toString(36).substr(2, 9),
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    file: file,
                    status: 'pending',
                    preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null
                }));
                
                this.files = [...this.files, ...filesToAdd];
            },
            
            removeFile(fileId) {
                this.files = this.files.filter(f => f.id !== fileId);
            },
            
            getFileIcon(type) {
                if (type.startsWith('image/')) return 'heroicon-o-photograph';
                if (type === 'application/pdf' || type.includes('word')) return 'heroicon-o-document-text';
                if (type.includes('excel') || type === 'text/csv') return 'heroicon-o-table';
                return 'heroicon-o-document';
            }
        }"
        class="space-y-4"
    >
        <input
            type="file"
            multiple
            class="hidden"
            x-ref="fileInput"
            @change="addFiles($event.target.files)"
        >
        
        <div
            class="relative min-h-[300px] rounded-lg border-2 border-dashed p-4"
            :class="{ 'border-primary-500 bg-primary-50': isDragging }"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
        >
            <template x-if="files.length === 0">
                <div class="flex h-full flex-col items-center justify-center space-y-2 text-center">
                    <x-heroicon-o-cloud-arrow-up class="h-12 w-12 text-gray-400"/>
                    <div class="space-y-1">
                        <p class="text-sm font-medium">
                            Drop files here or
                            <button type="button" class="text-primary-600 hover:text-primary-500" @click="$refs.fileInput.click()">
                                browse
                            </button>
                        </p>
                        <p class="text-xs text-gray-500">
                            Supported files: Images, PDF, Word, Excel, CSV
                        </p>
                    </div>
                </div>
            </template>

            <template x-if="files.length > 0">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <template x-for="file in files" :key="file.id">
                        <div class="group relative flex flex-col overflow-hidden rounded-lg border bg-white shadow-sm">
                            <button
                                @click="removeFile(file.id)"
                                class="absolute right-2 top-2 rounded-full bg-red-100 p-1 text-red-600 opacity-0 transition group-hover:opacity-100"
                                type="button"
                            >
                                <x-heroicon-m-x-mark class="h-4 w-4"/>
                            </button>
                            
                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex-1">
                                    <template x-if="file.preview">
                                        <img :src="file.preview" class="h-32 w-full object-cover" :alt="file.name">
                                    </template>
                                    <template x-if="!file.preview">
                                        <div class="flex h-32 items-center justify-center bg-gray-50">
                                            <x-dynamic-component
                                                :component="'heroicon-o-document'"
                                                x-bind:component="getFileIcon(file.type)"
                                                class="h-10 w-10 text-gray-400"
                                            />
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="mt-4 space-y-1">
                                    <p class="truncate text-sm font-medium text-gray-900" x-text="file.name"></p>
                                    <p class="text-xs text-gray-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': file.status === 'pending',
                                            'bg-green-100 text-green-800': file.status === 'uploaded'
                                        }"
                                        x-text="file.status"
                                    ></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex justify-end space-x-2">
            <x-filament::button
                type="button"
                color="gray"
                @click="files = []"
                x-show="files.length > 0"
            >
                Clear All
            </x-filament::button>
            
            <x-filament::button
                type="button"
                @click="$refs.fileInput.click()"
            >
                Add Files
            </x-filament::button>
        </div>
    </div>
</x-filament::page> 