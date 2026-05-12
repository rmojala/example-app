<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { create, show } from '@/actions/App/Http/Controllers/NoteController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({
  notes: {
    type: Object,
    required: true,
  },
  sharedNotes: {
    type: Object,
    required: true,
  },
  can: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <Head title="Notes" />

  <div class="space-y-8">
    <h1 class="text-2xl font-semibold">
      Your Notes
    </h1>

    <Button v-if="can.createNotes" as-child>
      <Link :href="create()">
        New
      </Link>
    </Button>

    <div v-else class="flex items-center gap-4">
      <Button disabled>
        New
      </Button>
      <span class="text-sm italic">
        Your account cannot create notes
      </span>
    </div>
  
    <ul
      v-if="notes.data.length > 0"
      class="list-disc pl-4 space-y-2 max-w-lg"
    >
      <li
        v-for="note in notes.data"
        :key="note.id"
      >
        <div class="flex items-center gap-2">
          <Link :href="show(note.id)">
            {{ note.title }}
          </Link>
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger>
                <Badge
                  v-if="note.sharedWithCount > 0"
                  class="h-4 min-w-4 rounded-full p-1 cursor-default"
                >
                  {{ note.sharedWithCount }}
                </Badge>
              </TooltipTrigger>
              <TooltipContent>
                <div>
                  Shared with {{ note.sharedWithCount }}
                  {{ note.sharedWithCount === 1 ? 'user' : 'users' }}
                </div>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </div>
      </li>
    </ul>
  
    <p v-else class="italic">
      You don't have any notes yet.
    </p>

    <template v-if="sharedNotes.data.length > 0">
      <h1 class="text-2xl font-semibold">
        Notes Shared With You
      </h1>

      <ul class="list-disc pl-4 space-y-2">
        <li
          v-for="note in sharedNotes.data"
          :key="note.id"
        >
          <div class="flex items-center gap-2">
            <Link :href="show(note.id)">
              {{ note.title }}
            </Link>
          </div>
        </li>
      </ul>
    </template>
  </div>
</template>
