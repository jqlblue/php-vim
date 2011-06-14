// Save this file as ptags.cpp and compile by
//              g++ -o ptags ptags.cpp
//*****************************************************************
// Copyright policy is GNU/GPL but additional request is
// that you include author's name and email on all copies
// Author : Al Dev Email: alavoor@yahoo.com
// Usage : ptags *.php3 *.inc
//                 This will generate a file called tags
//*****************************************************************
#include <iostream>
#include <fstream>
#include <stdio.h> // for sprintf
#include <stdlib.h> // for system
#include <string.h> // for memset
#include <ctype.h> // for isspace

#define BUFF_LEN  1024
#define LOCATION  9
using namespace std;

char *ltrim(char *dd);
char *rtrim(char *ee);

main(int argc, char **argv)
{
        if (argc < 2)
        {
                cerr << "\nUsage: " << argv[0] << " file .... " << endl;
                exit(0);
        }

        char fname[100] = "tag_file.out";
        FILE    *fpout;
        ofstream    fout(fname);
        if (fout.fail())
        {
                cerr << "\nError opening file : " << fname << endl;
                exit(-1);
        }
        //fpout = fopen(fname, "w");

        for (int ii = 1; ii < argc; ii++)
        {
                /*
                char buff[2024];

                sprintf(buff, "\\rm -f %s; ls %s > %s 2>/dev/null", outfile, argv[1], outfile);
                cout << "\nbuff = " << buff << endl;

                system(buff);
                fclose(fp);
                */
                FILE *fpin = NULL;
                fpin = fopen(argv[ii], "r");
                if (fpin == NULL)
                {
                        cerr << "\nError opening file : " << argv[ii] << endl;
                        exit(-1);
                }
                char buff[BUFF_LEN + 100];
                memset(buff, 0, BUFF_LEN +10);
                for ( ; fgets(buff, BUFF_LEN, fpin) != NULL; )
                {
                        char aa[BUFF_LEN + 100];
                        char aapointer[BUFF_LEN + 100];
                        memset(aa, 0, BUFF_LEN +10);
                        strcpy(aa, buff);
                        strcpy(aapointer, ltrim(aa));
                        strcpy(aa, aapointer);

                        // Remove the trailing new line..
                        {
                                int tmpii = strlen(aa);
                                if (aa[tmpii-1] == '\n')
                                        aa[tmpii-1] = 0;
                        }
                        //cout << "aa is : " << aa << endl;
                        //cout << "aapointer is : " << aapointer << endl;
                        if (strncmp(aa, "function ", LOCATION) != 0)
                                continue;
                        //cout << buff << endl;

                        // Example tags file output is like -
                        // al2  al.c    /^al2()$/;"     f
                        {
                                char bb[BUFF_LEN + 100];
                                memset(bb, 0, BUFF_LEN +10);
                                strcpy(bb, & aa[LOCATION]);
                                char *cc = bb;
                                while (cc != NULL && *cc != '(')
                                        *cc++;
                                *cc = 0;
                                cc = rtrim(bb);
                                //cout << "bb is : " << bb << endl;
                                //cout << cc << "\t" << argv[ii] << "\t" << "/^" << aa << "$/;\"\tf" << endl;
                                fout << cc << "\t" << argv[ii] << "\t" << "/^" << aa << "$/;\"\tf" << endl;
                                //fprintf(fpout, "%s\t%s\t/^%s$/;\"f\n", cc, argv[ii], aa );
                        }

                        memset(buff, 0, BUFF_LEN +10);
                }
                fclose(fpin);
        }
        fout.flush();
        fout.close();
        //fclose(fpout);

        // Sort and generate the tag file
        {
                char tmpaa[1024];
                sprintf(tmpaa, "sort %s > tags; \\rm -f %s", fname, fname);
                system(tmpaa);
        }
}

char *ltrim(char *dd)
{
    if (dd == NULL)
        return NULL;

    while (isspace(*dd))
        dd++;
        
        return dd;
}

char *rtrim(char *ee)
{
    if (ee == NULL)
        return NULL;

        int tmpii = strlen(ee) - 1;
        for (; tmpii >= 0 ; tmpii--)
        {
                if (isspace(ee[tmpii]) )
                {
                        //cout << "\nis a space!!" << endl;
                        ee[tmpii] = 0;
                }
        }
        return ee;
}