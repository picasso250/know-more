void beauty_print(const char * str)
{
    while (*str) {
        if (*str == '\r')
        {
            printf("\\r");
        }
        else if (*str == '\n')
            printf("\\n\n");
        else
            printf("%c", *str);
        str++;
    }
}
