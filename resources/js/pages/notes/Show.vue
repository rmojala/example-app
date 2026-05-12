<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { index, edit, destroy } from '@/actions/App/Http/Controllers/NoteController';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import SharedWithHelp from './components/SharedWithHelp.vue';

const props = defineProps({
  note: {
    type: Object,
    required: true,
  },
  isNoteOwner: {
    type: Boolean,
    required: true,
  },
});

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Notes',
        href: index(),
      },
      {
        title: 'View Note',
      },
    ],
  },
});

function deleteNote() {
  if (confirm('Are you sure you want to delete the note?')) {
    router.delete(destroy(props.note.data.id));
  }
}
</script>

<template>
  <Head title="View Note" />

  <div class="space-y-8">
    <h1 class="text-2xl font-semibold">
      View Note
    </h1>

    <div v-if="isNoteOwner" class="space-x-2">
      <Button as-child>
        <Link :href="edit(note.data.id)">
          Edit
        </Link>
      </Button>
      <Button variant="destructive" @click="deleteNote">
        Delete
      </Button>
    </div>

    <div class="max-w-lg space-y-4">
      <div v-if="!isNoteOwner" class="space-y-2">
        <Label>Author</Label>
        <p>{{ note.data.user.email }}</p>
      </div>

      <div class="space-y-2">
        <Label>Title</Label>
        <p>{{ note.data.title }}</p>
      </div>

      <div class="space-y-2">
        <Label>Details</Label>
        <p v-if="note.data.details">
          {{ note.data.details }}
        </p>
        <p v-else class="italic">
          This note does not have details.
        </p>
      </div>

      <div v-if="isNoteOwner" class="space-y-2">
        <div class="flex items-center gap-2">
          <Label>Shared With</Label>
          <SharedWithHelp />
        </div>
        <div v-if="note.data.sharedWith.length > 0">
          <ul class="list-disc pl-4 space-y-2">
            <li v-for="user in note.data.sharedWith" :key="user.id">
              {{ user.email }}
            </li>
          </ul>
        </div>
        <p v-else class="italic">
          This note is not shared with anyone.
        </p>
      </div>
    </div>
  </div>
</template>
