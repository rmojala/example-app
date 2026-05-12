<script setup>
import { CircleQuestionMark } from 'lucide-vue-next';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';
</script>

<template>
  <TooltipProvider>
    <Tooltip>
      <TooltipTrigger>
        <CircleQuestionMark :size="16" />
      </TooltipTrigger>
      <TooltipContent>
        <div>
          These users have read access to the note
        </div>
      </TooltipContent>
    </Tooltip>
  </TooltipProvider>
</template>
